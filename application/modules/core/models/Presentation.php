<?php
/**
 * CORE Conference Manager
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.terena.org/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to webmaster@terena.org so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2011 TERENA (http://www.terena.org)
 * @license    http://www.terena.org/license/new-bsd     New BSD License
 * @revision   $Id$
 */

/** 
 *
 * @package Core_Model
 */
class Core_Model_Presentation extends TA_Model_Acl_Abstract
{

	/**
	 * Get user by id
	 * @param		integer		$id		User id
	 * @return		Core_Resource_User_Item
	 */
	public function getPresentationById($id)
	{
		$row = $this->getResource('presentations')->getPresentationById( (int) $id );
    	if ($row === null) {
    		throw new TA_Model_Exception('id not found');
    	}
    	return $row;
	}

	/**
	 * Get all presentation data by id
	 * @param		integer		$id		presentation_id
	 * @return		Core_Resource_Presentation_Item
	 */
	public function getAllPresentationDataById($id)
	{
		$row = $this->getResource('presentations')->getPresentationById( (int) $id );
		if ($row === null) {
    		throw new TA_Model_Exception('id not found');
    	}
    	return $row;
	}

	/**
	 * Gets array of presentations for use in select box.
	 *
	 * @return	array
	 */
	public function getPresentationsForSelect($empty = null, $truncate = true)
	{
		if (!$this->checkAcl('list')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }
        if ($truncate) {
			return array_map(function($val) {
				return substr($val, 0, 50);
			}, $this->getResource('presentations')->getPresentationsForSelect($empty));
		}
		return $this->getResource('presentations')->getPresentationsForSelect($empty);
	}

	/**
	 * Link submissions to presentation
	 *
	 * @param	Core_Resource_Submission_Set $submissions RowSet of submissionsview
	 * @param	array	$post
	 * @return	Zend_Db_Statement_Pdo on success False on failure
	 */
	public function linkSubmissions(Core_Resource_Submission_Set $submissions, $post = array())
	{
		$form = $this->getForm('submitImport');
		// validate form
		if (!$form->isValid($post)) {
			return false;
		}
		$values = $form->getValues();

		// import submissions
		$valuesPu = $this->getResource('presentations')->linkSubmissions(
			$submissions, 
			new Zend_Config($values)
		);

		// give users the 'presenter' role
		if ($values['set_role']) {

			foreach ($submissions->toArray() as $val) {
				$userIds[] = $val['user_id'];
			}

			$this->getResource('userroles')->saveRows(array(
				'role_id' => $this->getResource('roles')->getRoleIdByName('presenter'),
				'user_id' => $userIds
			));
		}

		// link submitter (user) to presentation, eg. make user a speaker for this presentation
		if ($values['set_link']) {
			// @todo: gives notice, investigate!
			$this->getResource('presentationsusers')->saveRows($valuesPu);
		}

		return $valuesPu;
	}

	/**
     * Save user/presentation link
     *
     * @param	array	$post	Post variable
     * @return	mixed	The primary key of the inserted record if insert went OK
     *					false if user already belongs to this presentation
     */
	public function saveSpeakers(array $post)
	{
		if (!$this->checkAcl('speakersSave')) {
			throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		$form = $this->getForm('presentationUser');
		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		// get filtered values
		$values = $form->getValues();

		$resource = $this->getResource('presentationsusers');

		// if user presentation link does not already exist, save link
		if (!$resource->getItemByValues($values)) {
			return $resource->saveRow($values);
		} else {
			return false;
		}
	}

	/**
	 * Delete a user from this presentation
	 *
	 * @param	integer		$id		presentation_user_id
	 * @return	The number of rows deleted
	 */
	public function deleteSpeaker($id = null)
	{
		if (!$this->checkAcl('speakerDelete')) {
            #throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		if (!$id) {
			return false;
		}
		$id = (int) $id;

		return $this->getResource('presentationsusers')->getItemById($id)->delete();
	}


	/**
     * Save files and user/files link
     *
     * @param	array	$post	Post variable
     * @return	mixed	The primary key of the inserted record if insert went OK
     *					false if file already belongs to this presentation
     */
	public function saveFiles(array $post)
	{
		if (!$this->checkAcl('filesSave')) {
			throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		$form = $this->getForm('presentationFiles');
		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		// get filtered file values
		$values = $form->files->getValues(true);

		$db = $this->getResource('files')->getAdapter();
		$db->beginTransaction();

		foreach ($values as $file => $value) {
			try {
				$element = $form->files->$file;
				if ($element->isUploaded()) {

					$hash = $element->getHash('sha1');

					$element->addFilter('rename', array(
		    		    'target' => Zend_Registry::get('config')->directories->uploads.$hash,
		    		    'overwrite' => true
		    		));

		    		$originalName = $element->getFileName();
		    		$element->receive();
					$fileInfo = $element->getFileInfo();
					$fileInfo[$file]['_filename_original'] = $originalName;
					$fileInfo[$file]['_filehash'] = $hash;
					$fileInfo[$file]['_filetype'] = $file;

					// persist file
					$values['file_id'][$file] = $this->getResource('files')->saveRow($fileInfo);
				}
			} catch (Zend_File_Transfer_Exception $e) {
				$db->rollBack();
				throw new TA_Model_Exception($e->getMessage());
			}
		}

		$values['presentation_id'] = $form->getValue('presentation_id');
		$return = $this->getResource('presentationsfiles')->saveRows($values);

		$db->commit();
		return $return;
	}

	/**
	 * Get linked files
	 *
	 * @param	integer		$id		presentation_id
	 * @return	Zend_Db_Table_Rowset with Core_Resource_File_Item
	 */
	public function getFiles($id)
	{
		$fileIds = $this->getResource('presentationsfiles')->getFilesByPresentationId($id);
		return $this->getResource('filesview')->getFilesByIds($fileIds);
	}

	/**
	 * Get a list of presentations
	 * @param		integer		$page	Page number to show
	 * @param		array		$order	Array with keys 'field' and 'direction'
	 * @return		array		Grid array with keys 'cols', 'primary', 'rows'
	 */
	public function getPresentations($paged=null, $order=array(), $filter=null, $unique=false)
	{
		if (!$this->checkAcl('list')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		return $this->getResource('presentationsview')->getPresentations($paged, $order, $filter, $unique);
	}

	/**
	 * Remove user from resource
	 * @param		integer		$id		Id of record to delete
	 * @return		boolean
	 */
	public function delete($id = null)
	{
		if (!$this->checkAcl('delete')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		if (!$id) {
			return false;
		}
		$id = (int) $id;

		return $this->getPresentationById($id)->delete();
	}

	/**
	 * Save presentation to resource
	 *
	 * @param		array	$post	Post request
	 * @param		string	$action	The subform part to validate against
	 * @return		mixed	The primary key of the inserted/updated record or resource error message
	 */
	public function savePresentation(array $post, $action = null)
	{
		// perform ACL check
		if (!$this->checkAcl('save')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

        // get different form based on action parameter
		$formName = ($action) ? 'presentation' . ucfirst($action) : 'presentation';
		$form = $this->getForm($formName);

		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		// get filtered values
		$values = $form->getValues();

		$presentation = array_key_exists('presentation_id', $values) ?
			$this->getResource('presentations')
				 ->getPresentationById($values['presentation_id']) : null;

		return $this->getResource('presentations')->saveRow($values, $presentation);
	}


}
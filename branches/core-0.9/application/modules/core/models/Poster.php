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
 * @revision   $Id: Poster.php 623 2011-09-29 13:25:34Z gijtenbeek $
 */

/** 
 *
 * @package Core_Model
 */
class Core_Model_Poster extends TA_Model_Acl_Abstract
{

	/**
	 * Get poster by id
	 * @param		integer		$id		User id
	 * @return		Core_Resource_User_Item
	 */
	public function getPosterById($id)
	{
		$row = $this->getResource('posters')->getPosterById( (int) $id );
    	if ($row === null) {
    		throw new TA_Model_Exception('id not found');
    	}
    	return $row;
	}

	/**
	 * Get a list of posters
	 * @param		integer		$page	Page number to show
	 * @param		array		$order	Array with keys 'field' and 'direction'
	 * @param		boolean		$group	Group rows by date
	 * @return		array		Grid array with keys 'cols', 'primary', 'rows'
	 */
	public function getPosters($paged=null, $order=array(), $group = false)
	{
		if (!$this->checkAcl('list')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

        $items = $this->getResource('posters')->getPosters($paged, $order);

		if ($group) {
			$items['rows'] = $items['rows']->group();
		}
		return $items;
	}

	/**
	 * Remove poster from resource
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

		return $this->getPosterById($id)->delete();
	}

	/**
	 * Save poster to resource
	 *
	 * @param		array	$post	Post request
	 * @param		string	$action	The subform part to validate against
	 * @return		mixed	The primary key of the inserted/updated record or resource error message
	 */
	public function savePoster(array $post, $action = null)
	{
		// perform ACL check
		if (!$this->checkAcl('save')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

        // get different form based on action parameter
		$formName = ($action) ? 'poster' . ucfirst($action) : 'poster';
		$form = $this->getForm($formName);

		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		if ( $form->file->isUploaded() ) {
			// save file to filesystem
			try {
				$fileInfo = array();
				$adapter = $form->file->getTransferAdapter();
			    $hash = $adapter->getHash('sha1');

			    $form->file->addFilter('rename', array(
			        'target' => Zend_Registry::get('config')->directories->uploads.$hash,
			        'overwrite' => true
			    ));

			    $origName = $adapter->getFileName();
			    $adapter->receive();
				$fileInfo = $adapter->getFileInfo();
				$fileInfo['file']['_filename_original'] = $origName;
				$fileInfo['file']['_filehash'] = $hash;
				$fileInfo['file']['_filetype'] = 6;
			} catch (Zend_File_Transfer_Exception $e) {
				$e->getMessage();
			}
		}

		$db = $this->getResource('files')->getAdapter();
		$db->beginTransaction();

		try {
			// get filtered values
			$values = $form->getValues();

			if ( $form->file->isUploaded() ) {
				// persist file
				$fileId = $this->getResource('files')->saveRow($fileInfo);
				$values['file_id'] = $fileId;
			}

			$poster = array_key_exists('poster_id', $values) ?
				$this->getResource('posters')
					 ->getPosterById($values['poster_id']) : null;

			$return = $this->getResource('posters')->saveRow($values, $poster);

			$db->commit();

			return $return;

		} catch (Exception $e) {
			$db->rollBack();
			throw new TA_Model_Exception($e->getMessage());
		}
	}


}









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
class Core_Model_Location extends TA_Model_Acl_Abstract
{

	/**
	 * Get location by id
	 * @param		integer		$id		User id
	 * @return		Core_Resource_User_Item
	 */
	public function getLocationById($id)
	{
		$row = $this->getResource('locations')->getLocationById( (int) $id );
    	if ($row === null) {
    		throw new TA_Model_Exception('id not found');
    	}
    	return $row;
	}

	public function getLocationsForSelect($type = null)
	{
		if (!$this->checkAcl('list')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }
        return $this->getResource('locations')->getLocationsForSelect($type);
	}

	/**
	 * Get a list of locations
	 * @param		integer		$page	Page number to show
	 * @param		array		$order	Array with keys 'field' and 'direction'
	 * @return		array		Grid array with keys 'cols', 'primary', 'rows'
	 */
	public function getLocations($paged=null, $order=array())
	{
		if (!$this->checkAcl('list')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		return $this->getResource('locations')->getLocations($paged, $order);
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

		return $this->getLocationById($id)->delete();
	}

	/**
	 * Save location to resource
	 *
	 * @param		array	$post	Post request
	 * @param		string	$action	The subform part to validate against
	 * @return		mixed	The primary key of the inserted/updated record or resource error message
	 * @todo 		This can be abstracted, especially the save files stuff
	 */
	public function saveLocation(array $post, $action = null)
	{
		// perform ACL check
		if (!$this->checkAcl('save')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

        // get different form based on action parameter
		$formName = ($action) ? 'location' . ucfirst($action) : 'location';
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
				$fileInfo['file']['_filetype'] = 7;
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

			$location = array_key_exists('location_id', $values) ?
				$this->getResource('locations')
				 	 ->getLocationById($values['location_id']) : null;

			$return = $this->getResource('locations')->saveRow($values, $location);

			$db->commit();

			return $return;

		} catch (Exception $e) {
			$db->rollBack();
			throw new TA_Model_Exception($e->getMessage());
		}
	}


}









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
 * @revision   $Id: Event.php 623 2011-09-29 13:25:34Z gijtenbeek $
 */
 
/** 
 *
 * @package Core_Model
 */
class Core_Model_Event extends TA_Model_Acl_Abstract
{

	/**
	 * Get event by id
	 * @param		integer		$id		User id
	 * @return		Core_Resource_User_Item
	 */
	public function getEventById($id)
	{
		$row = $this->getResource('events')->getEventById( (int) $id );
    	if ($row === null) {
    		throw new TA_Model_Exception('id not found');
    	}
    	return $row;
	}

	public function getAllEventDataById($id)
	{
		$row = $this->getResource('eventsview')->getEventById( (int) $id );
    	if ($row === null) {
    		throw new TA_Model_Exception('id not found');
    	}
    	return $row;
	}

	/**
	 * Get a list of events
	 * @param		integer		$page	Page number to show
	 * @param		array		$order	Array with keys 'field' and 'direction'
	 * @param		boolean		$group	Group rows by 'day' or 'category'
	 * @return		array		Grid array with keys 'cols', 'primary', 'rows'
	 */
	public function getEvents($paged=null, $order=array(), $group=false)
	{
		if (!$this->checkAcl('list')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

        $items = $this->getResource('eventsview')->getEvents($paged, $order);

		if ($group) {
			$items['rows'] = $items['rows']->group($group);
		}
		return $items;
	}

	/**
	 * Get event categories
	 *
	 */
	public function getCategories()
	{
		if (!$this->checkAcl('category')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		return $this->getResource('eventcategories')->getCategories();
	}

	/**
	 * Remove event from resource
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

		return $this->getEventById($id)->delete();
	}

	/**
	 * Save event to resource
	 *
	 * @param		array	$post	Post request
	 * @param		string	$action	The subform part to validate against
	 * @return		mixed	The primary key of the inserted/updated record or resource error message
	 */
	public function saveEvent(array $post, $action = null)
	{
		// perform ACL check
		if (!$this->checkAcl('save')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

        // get different form based on action parameter
		$formName = ($action) ? 'event' . ucfirst($action) : 'event';
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
				$fileInfo['file']['_filetype'] = 2;
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

			$event = array_key_exists('event_id', $values) ?
				$this->getResource('events')
					 ->getEventById($values['event_id']) : null;

			$return = $this->getResource('events')->saveRow($values, $event);

			$db->commit();

			return $return;

		} catch (Exception $e) {
			$db->rollBack();
			throw new TA_Model_Exception($e->getMessage());
		}
	}


}









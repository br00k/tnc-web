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
 * @revision   $Id: Session.php 41 2011-11-30 11:06:22Z gijtenbeek@terena.org $
 */

/** 
 *
 * @package Core_Model
 * @author Christian Gijtenbeek
 */
class Core_Model_Session extends TA_Model_Acl_Abstract
{

	/**
	 * Get session by id
	 * @param		integer		$id		session_id
	 * @return		Core_Resource_Session_Item
	 */
	public function getSessionById($id)
	{
		$row = $this->getResource('sessions')->getSessionById( (int) $id );
    	if ($row === null) {
    		throw new TA_Model_Exception('id not found');
    	}
    	return $row;
	}

	/**
	 * Get all session data by id
	 * @param		integer		$id		session_id
	 * @return		Core_Resource_Sessionview_Item
	 */
	public function getAllSessionDataById($id)
	{
		$row = $this->getResource('sessionsview')->getSessionById( (int) $id );
    	if ($row === null) {
    		throw new TA_Model_Exception('id not found');
    	}
    	return $row;
	}

	/**
	 * Get a list of sessions
	 * @param		integer		$page	Page number to show
	 * @param		array		$order	Array with keys 'field' and 'direction'
	 * @return		array		Grid array with keys 'cols', 'primary', 'rows'
	 */
	public function getSessions($paged, $order)
	{
		if (!$this->checkAcl('list')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		return $this->getResource('sessions')->getSessions($paged, $order);
	}

	/**
	 * Testing google sync
	 * @todo: remove if not needed
	 */
	public function getAllSessionData()
	{
		if (!$this->checkAcl('list')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		return $this->getResource('sessionsview')->getAllSessionData();
	}

	/**
	 * Get session by time and location
	 *
	 * @param	$time		integer		timeslot_id
	 * @param	$location	integer		location_id
	 * @return	mixed		Core_Resource_Session_Item or false if no session found
	 */
	public function getSessionByTimeAndLocation($time, $location)
	{
		return $this->getResource('sessions')->getSessionByTimeAndLocation($time, $location);
	}

	/**
	 * Gets array of sessions for use in select box.
	 *
	 * @param	integer		$conferenceId
	 * @param	string		$empty
	 * @return	array
	 */
	public function getSessionsForSelect($conferenceId = null, $empty = null)
	{
		return $this->getResource('sessions')->getSessionsForSelect($conferenceId, $empty);
	}

	/**
	 * Get linked files
	 *
	 * @param	integer		$id		presentation_id
	 * @return	Zend_Db_Table_Rowset with Core_Resource_File_Item
	 */
	public function getFiles($id)
	{
		$fileIds = $this->getResource('sessionsfiles')->getFilesBySessionId($id);
		return $this->getResource('filesview')->getFilesByIds($fileIds);
	}

	/**
	 * Get evaluation of a specific session
	 *
	 * @param	integer		$sessionId		session_id
	 * @return array or null
	 */
	public function getEvaluationBySessionId($sessionId)
	{
		$evaluation = $this->getResource('sessionsevaluation')->getEvaluationBySessionId($sessionId);
		if ($evaluation) {
			return $evaluation->toArray();
		}
		return null;
	}

	/**
	 * Store evaluation
	 *
	 * @param		array	$post	Post request
	 * @return		mixed	The primary key of the inserted/updated record or resource error message
	 */
	public function saveEvaluation(array $post)
	{
		// perform ACL check
		if (!$this->checkAcl('evaluate')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		$form = $this->getForm('sessionEvaluation');

		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		$values = $form->getValues();
		$values['user_id'] = Zend_Auth::getInstance()->getIdentity()->user_id;

		$evaluation = array_key_exists('session_evaluation_id', $values) ?
			$this->getResource('sessionsevaluation')
				 ->getItemById($values['session_evaluation_id']) : null;

		return $this->getResource('sessionsevaluation')->saveRow($values, $evaluation);
	}

	/**
	 * Remove session from resource
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

		return $this->getSessionById($id)->delete();
	}

	/**
	 * Remove presentation from session
	 *
	 * @param	integer		$id		session_presentation_id
	 */
	public function deletePresentation($id = null)
	{
		if (!$this->checkAcl('presentationDelete')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		if (!$id) {
			return false;
		}
		$id = (int) $id;

		return $this->getResource('sessionspresentations')->getItemById($id)->delete();
	}

	/**
	 * Save session to resource
	 *
	 * @param		array	$post	Post request
	 * @param		string	$action	The subform part to validate against
	 * @return		mixed	The primary key of the inserted/updated record or resource error message
	 */
	public function saveSession(array $post, $action = null)
	{
		// perform ACL check
		if (!$this->checkAcl('save')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

        // get different form based on action parameter
		$formName = ($action) ? 'session' . ucfirst($action) : 'session';
		$form = $this->getForm($formName);

		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		$values = $form->getValues();

		$session = array_key_exists('session_id', $values) ?
			$this->getResource('sessions')
				 ->getSessionById($values['session_id']) : null;

		return $this->getResource('sessions')->saveRow($values, $session);
	}

	/**
	 * Save multiple sessions (only used by Sync for now)
	 *
	 * @param	array	$valuesMulti	multidim array of session value arrays
	 * @return	void
	 * @todo	add proper return value
	 */
	public function saveSessions($valuesMulti)
	{
		// perform ACL check
		if (!$this->checkAcl('save')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		$sessions = $this->getResource('sessions');

        foreach ($valuesMulti as $values) {
 			$session = array_key_exists('session_id', $values) ?
				$sessions->getSessionById($values['session_id']) : null;

			$session::detachStaticObserver(new Core_Model_Observer_Sessiongcal());

			$sessions->saveRow($values, $session);
		}
	}

	/**
     * Save files and user/files link
     *
     * @param	array	$post	Post variable
     * @return	mixed	The primary key of the inserted record if insert went OK
     *					false if user already belongs to this presentation
     * @todo this code is very similar to saveFiles in the presentation model. Abstract it!
     */
	public function saveFiles(array $post)
	{
		if (!$this->checkAcl('filesSave')) {
			throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		$form = $this->getForm('sessionFiles');
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

		$values['session_id'] = $form->getValue('session_id');
		$return = $this->getResource('sessionsfiles')->saveRows($values);

		$db->commit();
		return $return;
	}

	/**
	 * Delete a user from this session
	 *
	 * @param	integer		$id		session_user_id
	 * @return	The number of rows deleted
	 */
	public function deleteChair($id = null)
	{
		if (!$this->checkAcl('chairDelete')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		if (!$id) {
			return false;
		}
		$id = (int) $id;

		return $this->getResource('sessionsusers')->getItemById($id)->delete();
	}

	/**
     * Save user/session link
     *
     * @param	array	$post	Post variable
     * @return	mixed	The primary key of the inserted record if insert went OK
     *					false if user already belongs to this session
     */
	public function saveChairs(array $post)
	{
		if (!$this->checkAcl('chairSave')) {
			throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		$form = $this->getForm('sessionUser');
		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		$values = $form->getValues();

		$resource = $this->getResource('sessionsusers');

		// if user session link does not already exist, save link
		if (!$resource->getItemByValues($values)) {
			// give user session chair role
			$this->getResource('users')->addUserRole($values['user_id'], 'chair');
			return $resource->saveRow($values);
		} else {
			return false;
		}
	}

	/**
	 * Change order of presentations in a session
	 * This method only updates the rows that have changed order.
	 *
	 * @param	string	$sessionId	session_id
	 * @param	array	$newOrder	Array of session_presentation_id's ordered by displayorder
	 * @return	void
	 */
	public function setPresentationOrder($sessionId, $newOrder)
	{
		if (!$this->checkAcl('presentationOrder')) {
			throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		$resource = $this->getResource('sessionspresentations');
		$oldOrder = $resource->getPresentationOrder($sessionId);

		foreach ($newOrder as $order => $id) {
			$id = intval($id);
			// Current session_presentation_id has different place in array
			// so order has changed for this id.
			if ($oldOrder[$order] != $id) {
				$resource->updatePresentationOrder($id, $order+1);
			}
		}

	}


	/**
     * Save presentation/session link
     *
     * @param	array	$post	Post variable
     * @return	mixed	Zend_Db_Table_Row if presentation belongs to another session
     *					false if presentation already belongs to this session
     *					The primary key of the inserted record if insert went OK
     */
	public function savePresentations(array $post)
	{
		if (!$this->checkAcl('presentationsSave')) {
			throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		$form = $this->getForm('sessionPresentation');
		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		$values = $form->getValues();

		$resource = $this->getResource('sessionspresentations');

		// if presentation session link does not already exist
		if (!$resource->getItemByValues($values)) {
			// if presentation is not linked to another session
			if ($item = $resource->getItemByPresentationId($values)) {
				return $item;
			}
			return $resource->saveRow($values);
		} else {
			return false;
		}
	}

	/**
	 * Subscribe user to session
	 *
	 * @param	integer		$id		session_id
	 * @return	boolean
	 */
	public function subscribeUser($id)
	{
		$values = array(
			'session_id' => $id,
			'user_id' => Zend_Auth::getInstance()->getIdentity()->user_id
		);

		$resource = $this->getResource('subscriberssessions');

		// if user is not already subscribed
		if (!$resource->getItemByValues($values)) {
			return $resource->saveRow($values);
		} else {
			return false;
		}
	}

	/**
	 * Unsubscribe user from session
	 *
	 * @param	integer		$id		session_id
	 * @return	boolean
	 */
	public function unsubscribeUser($id)
	{
		$values = array(
			'session_id' => $id,
			'user_id' => Zend_Auth::getInstance()->getIdentity()->user_id
		);

		$resource = $this->getResource('subscriberssessions');

		if ($item = $resource->getItemByValues($values)) {
			return $item->delete();
		} else {
			return false;
		}
	}

	/**
	 * Get sessions user is subscribed to
	 *
	 * @param	integer		$userId	user_id
	 * @param	integer		$sessionId	session_id
	 * @return	mixed		array of session_id's
	 */
	public function getSubscriptions($userId = null, $sessionId = null)
	{
		return $this->getResource('subscriberssessions')->getSubscriptions( $userId, $sessionId );
	}

	/**
	 * Move a session
	 *
	 * @param	$data	array	Session data in the form:
	 *							<timeslot_id>-<location_id>
	 */
	public function moveSession($data)
	{
		$sessions = array();

		if (count($data) !== 2) {
			throw new TA_Model_Exception('This method needs two sessions');
		}

		// cast data values to named variables
		list($slots[0]['timeslot_id'], $slots[0]['location_id']) = array_map(function($v){ return intval($v); },explode("-", $data[0]));
		list($slots[1]['timeslot_id'], $slots[1]['location_id']) = array_map(function($v){ return intval($v); },explode("-", $data[1]));

		// get sessions and if they exist add them to an array
		if ($session = $this->getSessionByTimeAndLocation($slots[0]['timeslot_id'], $slots[0]['location_id'])) {
			$sessions[0] = $session;
		}
		if ($session = $this->getSessionByTimeAndLocation($slots[1]['timeslot_id'], $slots[1]['location_id'])) {
			$sessions[1] = $session;
		}

		switch (count($sessions)) {
			case 0:
			    throw new TA_Model_Exception('You have chosen two empty slots in the programme. Please select at least 1 occupied slot');
			break;
			case 1:
			    // updating one session
			    return $this->getResource('sessions')->saveRow(
			    	(key($sessions) == 0) ? $slots[1] : $slots[0],
			    	reset($sessions)
			    );
			break;
			case 2:
		    	// swapping two sessions, to avoid db restriction first unset fields, then add them again
		    	$sessionId = $this->getResource('sessions')->saveRow(
		    	   array('timeslot_id' => null, 'location_id' => null),
		    	   $sessions[0]
		    	);
		    	$this->getResource('sessions')->saveRow(
		    		$slots[0],
		    		$sessions[1]
		    	);
		    	$this->getResource('sessions')->saveRow(
		    		$slots[1],
		    		$this->getSessionById($sessionId)
		    	);
		    break;
		}

	}
}
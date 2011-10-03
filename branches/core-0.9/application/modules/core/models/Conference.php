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
 * @revision   $Id: Conference.php 623 2011-09-29 13:25:34Z gijtenbeek $
 */

/** 
 *
 * @package Core_Model
 */
class Core_Model_Conference extends TA_Model_Acl_Abstract
{

	/**
	 * Get conference by id
	 *
	 * @param		integer		$id		Conference_id
	 * @return		Core_Resource_Conference_Item
	 */
	public function getConferenceById($id)
	{
		$row = $this->getResource('conferences')->getConferenceById( (int) $id );
    	if ($row === null) {
    		throw new TA_Model_Exception('id not found');
    	}
    	return $row;
	}

	/**
	 * Get conference row by hostname
	 *
	 * @param	string	$hostname
	 * @return	object	Core_Resource_Conference_Item
	 */
	public function getConferenceByHostname($hostname)
	{
		$row = $this->getResource('conferences')->getConferenceByHostname($hostname);
    	if ($row === null) {
    		throw new TA_Model_Exception('hostname not found');
    	}
    	return $row;

	}

	/**
	 * Get a list of conferences
	 *
	 * @param		integer		$page	Page number to show
	 * @param		array		$order	Array with keys 'field' and 'direction'
	 * @return		array		Grid array with keys 'cols', 'primary', 'rows'
	 */
	public function getConferences($paged, $order)
	{
		if (!$this->checkAcl('list')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		return $this->getResource('conferences')->getConferences($paged, $order);
	}


	/**
	 * Create batch of timeslots
	 *
	 * @param	array	$post	Post request
	 */
	public function createTimeslots(array $post)
	{
		return $this->getResource('conferences')->createTimeslots($post);
	}


	/**
	 * Remove item from resource
	 *
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

		return $this->getConferenceById($id)->delete();
	}

	/**
	 * Save conference to resource
	 *
	 * @param		array	$post	Post request
	 * @param		string	$action	The subform part to validate against
	 * @return		mixed	The primary key of the inserted/updated record or resource error message
	 */
	public function saveConference(array $post, $action = null)
	{
		// perform ACL check
		if (!$this->checkAcl('save')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

        // get different form based on action parameter
		$formName = ($action) ? 'conference' . ucfirst($action) : 'conference';
		$form = $this->getForm($formName);

		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		// get filtered values
		$values = $form->getValues();

		$conference = array_key_exists('conference_id', $values) ?
			$this->getResource('conferences')
				 ->getConferenceById($values['conference_id']) : null;

		$conferenceId = $this->getResource('conferences')->saveRow(
			$conferenceValues = $form->getValues(true),
			$conference
		);
		return $conferenceId;
	}

	/**
	 * Get timeslots of certain type belonging to a conference
	 *
	 * @param	mixed		$type			integer for specific type or false for all
	 * @param	integer		$conferenceId	conference_id
	 */
	public function getTimeslots($type = false, $conferenceId = null)
	{
		return $this->getResource('timeslots')->getTimeslots($type, $conferenceId);
	}

	/**
	 * Save timeslots of resource
	 *
	 * @param		array	$post	Post request
	 * @param		string	$action	The subform part to validate against
	 * @return		mixed	The primary key of the inserted/updated record or resource error message
	 */
	public function saveTimeslots(array $post, $action = null)
	{
		if (!$this->checkAcl('savetimeslots')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		$formName = ($action) ? 'conferenceTimeslots' . ucfirst($action) : 'conferenceTimeslots';
		$form = $this->getForm($formName);

		if (!$form->isValid($post)) {
			return false;
		}

		$values = $form->getValues();

		return $this->getResource('timeslots')->saveRows($values);
	}


}









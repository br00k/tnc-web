<?php

class Core_Model_Timeslot extends TA_Model_Acl_Abstract
{
	/**
	 * Get timeslots for select box of certain type belonging to a conference
	 *
	 * @param	mixed		$type			integer for specific type or false for all
	 * @param	integer		$conferenceId	conference_id
	 */
	public function getTimeslotsForSelect($type = false, $conferenceId = null)
	{
		if (!$this->checkAcl('listSelect')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }
        return $this->getResource('timeslots')->getTimeslotsForSelect($type, $conferenceId);
	}

}
<?php

class Core_Model_Eventlog extends TA_Model_Acl_Abstract
{

	/**
	 * Get event log by event_type
	 * @param		string		$type
	 * @return		Zend_Db_Table_Row_Abstract
	 */
	public function getItemByType($type)
	{
		$row = $this->getResource('eventlogs')->getItemByType($type);
    	if ($row === null) {
    		throw new TA_Model_Exception('event log type not found');
    	}
    	return $row;
	}

	/**
	 * Save row in eventlog table
	 *
	 * @param		array	$data	Data to save
	 * @return		mixed	The primary key of the inserted/updated record or resource error message
	 */
	public function saveEventlog(array $data)
	{
		$row = $this->getResource('eventlogs')->getEventlogById($data['event_type']);
		return $this->getResource('eventlogs')->saveRow($data, $row);
	}

	/**
	 * Get eventlog by type
	 *
	 * @return Zend_Date timestamp of when event happened
	 */
	public function getTimestampByType($type)
	{
		if ($timestamp = $this->getResource('eventlogs')->getTimestampByType($type) ) {
			return new Zend_Date($timestamp, Zend_Date::ISO_8601);
		}
		return false;
	}

	public function getAllTimestamps()
	{
		//return $this->getResource('eventlogs')->getAllTimestamps()->toMagicArray();
		return $this->getResource('eventlogs')->getAllTimestamps();
	}

}









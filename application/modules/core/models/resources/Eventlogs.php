<?php
class Core_Resource_Eventlogs extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'eventlog';

	// compound primary key
	protected $_primary = array('event_type', 'conference_id');
	
	protected $_rowClass = 'Core_Resource_Eventlog_Item';
	
	protected $_rowsetClass = 'TA_Model_Resource_Db_Table_Rowset_Abstract';

	public function init() {}
	
	/**
	 * Get eventlog by primary key
	 *
	 * @return object Zend_Db_Table_Row_Abstract
	 */
	public function getEventlogById($id)
	{
		return $this->find($id, $this->getConferenceId())->current();
	}
	
	/**
	 * Get eventlog by type
	 *
	 * @return string timestamp of when event happened
	 */
	public function getTimestampByType($type)
	{
		return $this->getAdapter()->fetchOne(
			"select timestamp from " . $this->_name . " where event_type=:type and conference_id=:conference_id",
			array(
				'type' => $type, 
				'conference_id' => $this->getConferenceId()
			)
		);
	}
	
	public function getAllTimestamps()
	{
		return $this->getAdapter()->fetchAssoc(
			"select * from " . $this->_name . " where conference_id=:conference_id",
			array(
				'conference_id' => $this->getConferenceId()
			)
		);
	}
	
}
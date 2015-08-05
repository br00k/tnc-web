<?php
class Core_Resource_Events extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'events';

	protected $_primary = 'event_id';

	protected $_rowClass = 'Core_Resource_Event_Item';
	
	public function init() {}

	/**
	 * Gets deadline by primary key
	 * @return object Zend_Db_Table_Row
	 */
	public function getEventById($id)
	{
		return $this->find( (int)$id )->current();
	}

}
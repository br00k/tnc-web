<?php
class Core_Resource_Deadlines extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'deadlines';

	protected $_primary = 'deadline_ie';

	public function init() {}

	/**
	 * Gets deadline by primary key
	 * @return object Zend_Db_Table_Row
	 */
	public function getDeadlineById($id)
	{
		return $this->find( (int)$id )->current();
	}

}
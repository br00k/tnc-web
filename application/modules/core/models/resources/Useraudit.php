<?php
class Core_Resource_Useraudit extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'useraudit';

	protected $_primary = 'useraudit_id';

	public function init() {}

	/**
	 * Gets user by primary key
	 * @return object Core_Resource_User_Item
	 */
	public function getUserById($id)
	{
		return $this->find( (int)$id )->current();
	}

}
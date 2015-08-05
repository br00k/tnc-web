<?php
class Core_Resource_Sessionsusers extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'sessions_users';

	protected $_primary = 'session_user_id';

	public function init() {}

	/**
	 * Gets item by primary key
	 * @return object Zend_Db_Table_Row
	 */
	public function getItemById($id)
	{
		return $this->find( (int)$id )->current();
	}

	/**
	 * returns item based on id values
	 *
	 * @param	array	$data	Session_id and User_id values
	 * @return	object	Zend_Db_Table_Row
	 */
	public function getItemByValues(array $data)
	{
		return $this->fetchRow(
					$this->select()
					->where('session_id = ?', $data['session_id'])
					->where('user_id = ?', $data['user_id'])
				);
	}

}
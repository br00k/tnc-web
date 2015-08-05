<?php
class Core_Resource_Userroles extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'user_role';

	protected $_primary = 'user_role_id';

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
	 * @param	array	$data	user_id and role_id values
	 * @return	object	Zend_Db_Table_Row
	 */
	public function getItemByValues(array $data)
	{
		return $this->fetchRow(
					$this->select()
					->where('user_id = ?', $data['user_id'])
					->where('role_id = ?', $data['role_id'])
				);
	}

	/**
	 * Save rows to the database. (insert or update)
	 *
	 * @param 	array 	$values
	 * @return	Zend_Db_Statement_Pdo on success or void if nothing is inserted
	 */
	public function saveRows($values = array())
	{
		$db = $this->getAdapter();

		// get current user_id/role_id combinations
		$currentValues = $db->fetchCol(
			$this->select()
			->from($this->_name, array('user_id'))
			->where('user_id IN (?)', $values['user_id'])
			->where('role_id = ?', $values['role_id'])
		);

		foreach ($values['user_id'] as $val) {
			if (!in_array($val, $currentValues)) {
				// using $val as key keeps the entries unique (in case one user submitted multiple papers)
				$insertValues[$val] = '('. $val . ',' . $values['role_id'] .')';
			}
		}
		
		// only insert users if they are not already inserted
		if (isset($insertValues)) {
			$query = "INSERT INTO " . $this->_name . " (user_id, role_id) VALUES ".implode(',', $insertValues);
			return $db->query($query);
		}
	}

}
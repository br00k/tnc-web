<?php
class Core_Resource_Presentationsusers extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'presentations_users';

	protected $_primary = 'presentation_user_id';

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
	 * @param	array	$data	presentation_id and user_id values
	 * @return	object	Zend_Db_Table_Row
	 */
	public function getItemByValues(array $data)
	{
		return $this->fetchRow(
					$this->select()
					->where('presentation_id = ?', $data['presentation_id'])
					->where('user_id = ?', $data['user_id'])
				);
	}

	/**
	 * Save rows to the database. (insert only)
	 *
	 * This method makes sure that only records that are not already in the
	 * many to many table get inserted
	 * 
	 * @param 	array 	$values
	 * @return	Zend_Db_Statement_Pdo on success or void if nothing is inserted
	 */
	public function saveRows()
	{
		$db = $this->getAdapter();

		// get current user_id/presentation_id combinations
		$currentValues = $db->fetchPairs(
			$this->select()
			->from($this->_name, array('user_id', 'presentation_id'))
		);

		$query = "select p.presentation_id, us.user_id
 					from submissions s
 					left join users_submissions us on (s.submission_id = us.submission_id)
 					left join presentations p on (p.submission_id = s.submission_id)
 					where s.submission_id IN (
 					select submission_id from presentations p
 					)
 					and s.conference_id=".$this->getConferenceId();

		$values = $db->fetchAll($query);

		foreach ($values as $val) {
			// if user/presentation link does not already exist
			if ( !$currentValues[$val['user_id']] == $val['presentation_id'] ) {
			    // using $val as key keeps the entries unique
			    $insertValues[$val['user_id']] = '('. $val['user_id'] . ',' . $val['presentation_id'] .')';
			}
		}

		if ($insertValues) {
			$query = "INSERT INTO " . $this->_name . " (user_id, presentation_id) VALUES ".implode(',', $insertValues);
			return $db->query($query);
		}
	}
}
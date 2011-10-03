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
 * @revision   $Id: Presentationsusers.php 613 2011-09-19 13:16:58Z visser $
 */
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
	public function saveRows(array $values)
	{
		$db = $this->getAdapter();

		// get current user_id/presentation_id combinations
		$currentValues = $db->fetchPairs(
			$this->select()
			->from($this->_name, array('presentation_id', 'user_id'))
		);

		// compare current values with values about to be inserted
		// I need to do this to prevent duplicate key constraint on
		// presentations_users_idx
		foreach ($values as $key => $value) {
			if (isset($currentValues[$key])) {
				if ($currentValues[$key] == $value) {
					continue;
				}
			}
			$insertValues[] = "(".(int)$key .",". (int)$value.")";
		}

		$insertValues = implode(',', $insertValues);

		if ($insertValues) {
			$query = "INSERT INTO " . $this->_name . " (presentation_id, user_id) VALUES ".$insertValues;
			return $db->query($query);
		}
	}
}
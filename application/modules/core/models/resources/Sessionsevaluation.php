<?php
class Core_Resource_Sessionsevaluation extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'session_evaluation';

	protected $_primary = 'session_evaluation_id';

	public function init() {}

	/**
	 * Gets item by primary key
	 * @return object Zend_Db_Table_Row
	 */
	public function getItemById($id)
	{
		return $this->find( (int)$id )->current();
	}

	public function getEvaluationBySessionId($sessionId)
	{
		return $this->fetchRow(
			$this->select()
				 ->where('session_id = ?', $sessionId)
		);
	}


}
<?php
class Core_Resource_Reviewerssubmissions extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'reviewers_submissions';

	protected $_primary = 'reviewer_submission_id';
	
	protected $_rowClass = 'Core_Resource_Review_Submission_Item';

	public function init()
	{
		#$this->attachObserver(new Core_Model_Observer_Reviewsubmission());
	}

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
	 * @param	array	$data	Submission_id and User_id values
	 * @return	object	Zend_Db_Table_Row
	 */
	public function getItemByValues(array $data)
	{
		return $this->fetchRow(
					$this->select()
					->where('submission_id = ?', $data['submission_id'])
					->where('user_id = ?', $data['user_id'])
				);
	}

}
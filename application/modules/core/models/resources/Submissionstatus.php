<?php
class Core_Resource_Submissionstatus extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'submission_status';

	protected $_primary = 'submission_status_id';
	
	// enabling this will fuck up Observers...look into this!
	// probably because this item is not the same as the class
	#protected $_rowClass = 'Core_Resource_Submission_Item';

	public function init() {}

	/**
	 * Get submission status by submission_id
	 *
	 * @param	integer		$id		submission_id
	 * @return object Core_Resource_Submission_Item
	 */
	public function getStatusBySubmissionId($id)
	{
		return $this->fetchRow(
					$this->select()
					->where('submission_id = ?', $id)
				);
	}
	
	/**
	 * Gets submission status by primary key
	 *
	 * @return object Core_Resource_Submission_Item
	 */
	public function getSessionById($id)
	{
		return $this->find( (int)$id )->current();
	}
}
<?php
/**
	* This class represents a single record
	* Methods in this class could include logic for a single record
	* for example sticking first_name and last_name together in a getName() method
	*/
class Core_Resource_Submission_Item extends TA_Model_Resource_Db_Table_Row_Abstract
{

	/**
	 * Get reviewers belonging to this submission
	 *
	 * @return array
	 */
	public function getReviewers()
	{
		$query = "select u.email, rs.reviewer_submission_id as id from reviewers_submissions rs
		left join users u on (rs.user_id = u.user_id)
		where rs.submission_id=:submission_id";

		return $this->getTable()->getAdapter()->query(
			$query, array(':submission_id' => $this->submission_id)
		)->fetchAll();
	}
	
	public function getSubmissionOneliner()
	{
		return $this->title;
	}
}
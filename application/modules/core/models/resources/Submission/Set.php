<?php
/**
 * This class represents a rowset
 */
class Core_Resource_Submission_Set extends Zend_Db_Table_Rowset_Abstract
{
	/**
	 * ?? what is this doing here? Is this called from anywhere??
	 *
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

	/**
	 * @todo: This can be removed?? belongs in Item.php
	 *
	 */
	public function getSubmissionOneliner()
	{
		return $this->title;
	}

	/**
	 * Get all reviewers
	 *
	 * @todo: rename this to getReviewers()
	 * @return array
	 */
	public function getAllReviewers()
	{
		$query = "select u.fname, u.lname, u.email, u.user_id, rs.submission_id from reviewers_submissions rs
		left join users u on (rs.user_id = u.user_id)";
		return $this->getTable()->getAdapter()->query($query)->fetchAll();
	}

	/**
	 * Get a list of reviews indexed by submission_id
	 *
	 * @param	$userId			integer		User id of reviewer to filter by
	 * @param	$groupUserId	boolean		Group list by user_id instead of review_id
	 * @return array
	 */
	public function getReviews($userId = null, $groupUserId = false)
	{
		$list = array();

		$query = "select user_id, submission_id, review_id, inserted from reviews";
		if ($userId) {
			$query .= " where user_id=".(int) $userId;
		}

		$reviews = $this->getTable()->getAdapter()->fetchAll($query);

		foreach ($reviews as $review) {
			$submission = current(array_filter($this->toArray(), function($val) use($review) {
			     return ($val['submission_id'] == $review['submission_id']);
			}));
			if ($groupUserId) {
				$list[$review['submission_id']][$review['user_id']] = $review;
			} else {
				$list[$review['submission_id']][$review['review_id']] = $review;
			}
		}

		return $list;
	}

	/**
	 * Get list of all reviewers and the submissions they should review
	 *
	 * @param	$todo	boolean		Only show submissions that are should still be reviewed
	 * @return array	contains: reviewers/submissions
	 */
	public function getReviewersSubmissions($todo = false)
	{
		$list = array();
		$reviews = $this->getReviews(null, true);
		$reviewers = $this->getAllReviewers();

		foreach ($reviewers as $reviewer) {
			if ( !isset($list[$reviewer['user_id']]) ) {
				$list[$reviewer['user_id']] = $reviewer;
			}

			$submission = current(array_filter($this->toArray(), function($val) use($reviewer, $reviews) {
				return ($val['submission_id'] == $reviewer['submission_id']);
			}));

			if ($todo) {
				if (!isset($reviews[$submission['submission_id']][$reviewer['user_id']])) {
				    // there is a review for this submission by this reviewer
				    $list[$reviewer['user_id']]['submission'][] = $submission;
				}
			} else {
				$list[$reviewer['user_id']]['submission'][] = $submission;
			}

		}
		if ($todo) {
			$list = array_filter($list, function($val) {
				// remove empty submissions
				return (array_key_exists('submission', $val));
			});
		}

		return $list;
	}

	/**
	 * Get total number of reviewers for a submission
	 * @return array
	 */
	public function getNumberOfReviewers()
	{
		$query = "select submission_id, count(*) from reviewers_submissions group by reviewers_submissions.submission_id";
		return $this->getTable()->getAdapter()->fetchPairs($query);
	}


}
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
 * @revision   $Id: Review.php 81 2012-12-05 09:47:00Z gijtenbeek@terena.org $
 */

/**
 *
 * @package Core_Model
 * @author Christian Gijtenbeek
 */
class Core_Model_Review extends TA_Model_Acl_Abstract
{

	/**
	 * Get submission by id
	 * @param		integer		$id		User id
	 * @return		Core_Resource_User_Item
	 */
	public function getReviewById($id)
	{
		$row = $this->getResource('reviews')->getReviewById( (int) $id );
    	if ($row === null) {
    		throw new TA_Model_Exception('id not found');
    	}
    	return $row;
	}

	/**
	 * Get a list of reviews
	 * @param		integer		$page		Page number to show
	 * @param		array		$order		Array with keys 'field' and 'direction'
	 * @param		integer		$filter		filter to apply to grid
	 * @param		boolean		$aclSkip	Skip ACL check (needed if called from observer)
	 * @return		array		Grid array with keys 'cols', 'primary', 'rows'
	 */
	public function getReviews($paged=null, $order=array(), $filter=null, $aclSkip=false)
	{
		if (!$aclSkip) {
			if (!$this->checkAcl('list')) {
        	    throw new TA_Model_Acl_Exception("Insufficient rights");
        	}
        }

		$conference = Zend_Registry::getInstance()->conference;

		$now = new Zend_Date();

		return $this->getResource('reviewsview')->getReviews($paged, $order, $filter);
	}

	/**
	 * Remove submission from resource
	 * @param		integer		$id		Id of record to delete
	 * @return		boolean
	 */
	public function delete($id = null)
	{
		if (!$this->checkAcl('delete')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		if (!$id) {
			return false;
		}
		$id = (int) $id;

		return $this->getReviewById($id)->delete();
	}

	/**
	 * Get list of all reviewers and the submissions they should review
	 *
	 * @param	boolean	$todo			Only show submissions that should still be reviewed
	 * @param	boolean	$tiebreakers	Remove tiebreaker submissions from list
	 * @return	array	contains: reviewers/submissions
	 */
	public function getReviewersForMail($todo = false, $tiebreakers = true)
	{
		if (!$this->checkAcl('mail')) {
			throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		$submissions = $this->getResource('submissions')->getSubmissions();

		if (empty($submissions)) {
			throw new TA_Exception('no submissions found');
		}

		$list = array();

		if ($todo) {
			$reviews = $this->getReviewsIndexedBySubmission($submissions['rows'], null, true);
		}
		$tiebreakers = $this->getAllTiebreakers();

		foreach ($this->getResource('reviewerssubmissions')->getAllReviewers() as $reviewer) {
			if ( !isset($list[$reviewer['user_id']]) ) {
				$list[$reviewer['user_id']] = $reviewer;
			}

			$submission = current(array_filter($submissions['rows']->toArray(),
				function($val) use($reviewer, $tiebreakers) {
					return ($val['submission_id'] == $reviewer['submission_id']);
				})
			);

			// remove non-tiebreaker submissions from list, only if user is a tiebreaker for the submission
			if ( ($reviewer['tiebreaker']) && ($tiebreakers) ) {
				if (isset($tiebreakers[$reviewer['submission_id']])) {
					if (!isset($tiebreakers[$reviewer['submission_id']]['tiebreak_required'])) {					
						$submission = null;
					}
				} else {
					$submission = null;
				}
			}
			
			if ($submission) {
				if ($todo) {
					// if there is a review for this submission by this reviewer
					if (!isset($reviews[$submission['submission_id']][$reviewer['user_id']])) {
					    $list[$reviewer['user_id']]['submission'][] = $submission;
					}
				} else {
					$list[$reviewer['user_id']]['submission'][] = $submission;
				}
			}

		}
		if ($todo) {
			// remove empty submissions
			$list = array_filter($list, function($val) {
				return (array_key_exists('submission', $val));
			});
		}

		return $list;
	}

	/**
	 * Get a list of reviews indexed by submission_id
	 *
	 * @param	Core_Resource_Submission_Set	$submissions
	 * @param	integer		$userId			User id of reviewer to filter by
	 * @param	boolean		$groupUserId	Group list by user_id instead of review_id
	 * @return array
	 */
	public function getReviewsIndexedBySubmission(Core_Resource_Submission_Set $submissions, $userId = null, $groupUserId = false)
	{
		$list = array();

		foreach ($this->getResource('reviews')->getReviewsIndexedBySubmission($userId) as $review) {
			$submission = current(array_filter($submissions->toArray(), function($val) use($review) {
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
	 * Save submission to resource
	 *
	 * @param		array	$post	Post request
	 * @param		string	$action	The subform part to validate against
	 * @return		mixed	The primary key of the inserted/updated record or resource error message
	 */
	public function saveReview(array $post, $action = null)
	{
		// perform ACL check
		if (!$this->checkAcl('save')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

        // get different form based on action parameter
		$formName = ($action) ? 'review' . ucfirst($action) : 'review';
		$form = $this->getForm($formName);

		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		$values = $form->getValues();
		// only set user_id if user is not admin or if the review is new
		if ( (!$this->getIdentity()->isAdmin()) || ($action != 'edit') ) {
			$values['user_id'] = $this->getIdentity()->user_id;
		}

		$review = array_key_exists('review_id', $values) ?
			$this->getResource('reviews')
				 ->getReviewById($values['review_id']) : null;

		return $this->getResource('reviews')->saveRow($values, $review);

	}

	/**
	 * Save Tiebreaker
	 *
	 * @param	array	$values
	 * @return	mixed	The primary key of the inserted/updated record or resource error message
	 */
	public function saveTiebreaker(array $values)
	{
		$reviewbreaker = array_key_exists('submission_id', $values) ?
			$this->getResource('reviewbreakers')
				 ->getItemById($values['submission_id']) : null;


		if (!$values['evalue']) {
			if ($reviewbreaker) {
				return $reviewbreaker->delete();
			}
			return;
		}

		return $this->getResource('reviewbreakers')->saveRow($values, $reviewbreaker);

	}

	/**
	 * Helper method to calculate the submissions that need a tiebreaker
	 *
	 * @param	array	$submissions
	 * @return	array
	 */
	private function _calculateTiebreakers($submissions, $excludeNoTiebreakNeeded = false)
	{
		$config = Zend_Registry::get('config');

		foreach ($submissions as $key => $val) {
			$e = (isset($val['evalue'])) ? $val['evalue'] : 0;

			if ( ($val['wrong_reviewer_count'] >= 1) && ($val['review_count'] <= 2) ) {
		    	$submissions[$key]['tiebreak_required'] = true;
			} elseif ($e > $config->core->review->tiebreaker) {
		    	$submissions[$key]['tiebreak_required'] = true;
		    	$submissions[$key]['_lod'] = round($e - $config->core->review->tiebreaker, 2);
		    } elseif ( isset($val['tiebreaker']) && ($excludeNoTiebreakNeeded) ) {
				if ($val['tiebreaker']) {
					unset($submissions[$key]);
				}
			}
		}

		return $submissions;
	}

	/**
	 * Get all submissions that require a tiebreaker
	 *
	 * @param 	integer		$userId			Defaults to logged in user
	 * @param	array		$submissionIds
	 * @return	array
	 */
	public function getAllTiebreakers($userId = null, $submissionIds = null)
	{
		$userId = (isset($userId))
			? $userId
			: Zend_Auth::getInstance()->getIdentity()->user_id;

		return $this->_calculateTiebreakers(
			$this->getResource('reviewbreakers')->getAllTiebreakers($userId, $submissionIds)
		);
	}

	/**
	 * Get submissions that the user is assigned reviewer of.
	 * For submissions that require a tiebreaker the 'tiebreak_required' key is set
	 *
	 * @param 	integer		$userId						Defaults to logged in user
	 * @param	boolean		$excludeReviewed			Exclude submissions that user reviewed
	 * @param	boolean		$excludeNoTiebreakNeeded	Exclude submissions of which I am tiebreaker but that require no tiebreak
	 * @return	array
	 */
	public function getPersonalTiebreakers($userId = null, $excludeReviewed = false, $excludeNoTiebreakNeeded = false)
	{
		$userId = (isset($userId))
			? $userId
			: Zend_Auth::getInstance()->getIdentity()->user_id;

		if (!$user = $this->getResource('users')->getUserById($userId)) {
			throw new Exception('There is no user with id '. $userId );
		}
		
		return $this->_calculateTiebreakers(
			$user->getSubmissionsToReview(true, $excludeReviewed),
			$excludeNoTiebreakNeeded
		);
	}

}
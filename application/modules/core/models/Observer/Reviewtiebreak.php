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
 * @revision   $Id: Review.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */

/**
 * Review observer.
 *
 * @package Core_Model
 * @subpackage Core_Model_Observer
 */
class Core_Model_Observer_Reviewtiebreak extends TA_Model_Acl_Abstract implements TA_Model_Observer_Interface
{

	public function _postInsert(TA_Model_Observed_Interface $subject, $msg)
	{
		$this->_saveTieBreaker($subject);
	}

	public function _postUpdate(TA_Model_Observed_Interface $subject, $msg)
	{
		$this->_saveTieBreaker($subject);
	}

	public function _postDelete(TA_Model_Observed_Interface $subject, $msg)
	{
		$this->_saveTieBreaker($subject);
	}

	/**
	 * Save tiebreaker value
	 *
	 */
	private function _saveTieBreaker($subject)
	{
		$config = Zend_Registry::get('conference');
		$reviewModel = new Core_Model_Review();

		// get reviews of submission
		$reviews = $reviewModel->getReviews(null, null, array(
			'submission_id' => $subject->submission_id
		), true);

		// trigger when user is wrong reviewer
		if (($subject->self_assessment == 1) && ($reviews['rows']->count() <= 2)) {
			$this->_mailTiebreakers($subject);
		} elseif ($reviews['rows']->count() == 2) {
			// store values
			$reviewModel->saveTiebreaker(array(
				'submission_id' => $subject->submission_id,
				'evalue' => $reviews['rows']->getEvalue()
			));
			// trigger when Level Of Disagreement is above threshold
			if ($reviews['rows']->getTieBreaker()) {
				$this->_mailTiebreakers($subject);
			}
		}
	}

	/**
	 * Send email to tiebreakers
	 *
	 */
	private function _mailTiebreakers($subject)
	{
		$config = Zend_Registry::get('config');
		if ($config->core->observer->tiebreaker->notify == 1) {
			$submitModel = new Core_Model_Submit();
			$submission = $submitModel->getSubmissionById($subject->submission_id);
			$users = $submission->getUsers(true);

			$viewParams = $submission->toArray();

			$mailer = new TA_Controller_Action_Helper_SendEmail();
			foreach ($users as $user) {
				$viewParams['fname'] = $user->fname;
				$viewParams['lname'] = $user->lname;
				$mailer->sendEmail(array(
				   'to_email' => $user->email,
				   'subject' => 'CORE Review needed',
				   'template' => 'review/tiebreaker'
				), $viewParams);
			}
		}
	}

}










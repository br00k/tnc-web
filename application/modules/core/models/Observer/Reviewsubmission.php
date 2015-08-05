<?php
/**
 * Review Submission observer.
 *
 * I can implement the following methods here: _postUpdate, _postInsert, _postDelete
 * This way I can have different behaviour for different actions. Eg, log a delete
 * and send an email upon insert/update
 *
 */
class Core_Model_Observer_Reviewsubmission extends TA_Model_Acl_Abstract implements TA_Model_Observer_Interface
{

	public function _postInsert(TA_Model_Observed_Interface $subject, $msg)
	{
		$conference = Zend_Registry::get('conference');
		$userModel = new Core_Model_User();
		$user = $userModel->getUserById($subject->user_id);

		$submitModel = new Core_Model_Submit();
		$submission = $submitModel->getSubmissionById($subject->submission_id);

		$viewParams = $submission->toArray();
		$viewParams['fname'] = $user->fname;
		$viewParams['lname'] = $user->lname;

		$mailer = new TA_Controller_Action_Helper_SendEmail();
		$mailer->sendEmail(array(
		   'to_email' => $user->email,
		   'subject' => 'CORE Observer: '.__CLASS__,
		   'template' => 'review/observer'
		), $viewParams);
	}

}
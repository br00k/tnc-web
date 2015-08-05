<?php
/**
 * Session observer.
 *
 * I can implement the following methods here: _postUpdate, _postInsert, _postDelete
 * This way I can have different behaviour for different actions. Eg, log a delete
 * and send an email upon insert/update
 *
 */
class Core_Model_Observer_Sessionsubscriber extends TA_Model_Acl_Abstract implements TA_Model_Observer_Interface
{

	private function _informSubscribers(TA_Model_Observed_Interface $subject, $msg)
	{
		$conference = Zend_Registry::get('conference');

		$sessionModel = new Core_Model_Session();
		$subscriptions = $sessionModel->getSubscriptions(null, $subject->session_id);

		if (!empty($subscriptions)) {

		  $mailer = new TA_Controller_Action_Helper_SendEmail();
		  $mailer->sendEmail(array(
		     'to_email' => $subscriptions,
		     'html' => true,
		     'subject' => $conference['name']. ': Session updated',
		     'template' => 'session/observer'
		  ), $subject->toArray());

		}
	}

	public function _postInsert(TA_Model_Observed_Interface $subject, $msg)
	{
	}

	public function _postUpdate(TA_Model_Observed_Interface $subject, $msg)
	{
	}

	public function _postDelete(TA_Model_Observed_Interface $subject, $msg)
	{
	}

}
<?php
/**
 * User observer. 
 *
 * The following methods can be implemented here: _postUpdate, _postInsert, _postDelete
 * This way I can have different behaviour for different actions. Eg, log a delete 
 * and send an email upon insert/update
 * 
 */
class Core_Model_Observer_User extends TA_Model_Acl_Abstract implements TA_Model_Observer_Interface
{

	public function _postInsert(TA_Model_Observed_Interface $subject, $msg)
	{
		$conference = Zend_Registry::get('conference');

		$mailer = new TA_Controller_Action_Helper_SendEmail();
		#$mailer->sendEmail(array(
		#	'html' => true,
		#	'to_email' => $conference['email'],
		#	'subject' => 'New user added to CORE',
		#	'template' => 'user/observer-insert'
		#), $subject->toArray());
	}
	
	/**
	 * @todo: this is the same as _postInsert, use different template
	 * and abstract away logic into separate methods
	 *
	 */
	public function _postUpdate(TA_Model_Observed_Interface $subject, $msg)
	{
		$conference = Zend_Registry::get('conference');

		#$mailer = new TA_Controller_Action_Helper_SendEmail();
		#$mailer->sendEmail(array(
		#	'html' => true,
		#	'to_email' => $conference['email'],
		#	'subject' => 'Existing user updated',
		#	'template' => 'user/observer-insert'
		#), $subject->toArray());

	}
}
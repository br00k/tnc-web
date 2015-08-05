<?php
/**
 * Review observer. 
 *
 * I can implement the following methods here: _postUpdate, _postInsert, _postDelete
 * This way I can have different behaviour for different actions. Eg, log a delete 
 * and send an email upon insert/update
 * 
 */
class Core_Model_Observer_Review extends TA_Model_Acl_Abstract implements TA_Model_Observer_Interface
{

	public function _postInsert(TA_Model_Observed_Interface $subject, $msg)
	{
		$conference = Zend_Registry::get('conference');
		$submitModel = new Core_Model_Submit();
		$userModel = new Core_Model_User();
		$values['review'] = $subject->toArray();		
		$values['submission'] = $submitModel->getSubmissionById($values['review']['submission_id'])->toArray();
		$values['user'] = $userModel->getUserById($values['review']['user_id'])->getSafeUser();

		$mailer = new TA_Controller_Action_Helper_SendEmail();
		$mailer->sendEmail(array(
		   'to_email' => $conference['email'],
		   'html' => true,
		   'subject' => 'CORE Observer: '.__CLASS__,
		   'template' => 'review/postinsert'
		), $values);
	}
}
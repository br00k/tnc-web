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
 * @revision   $Id: Sessiongcal.php 30 2011-10-06 08:37:15Z gijtenbeek@terena.org $
 */
 
/**
 * Session observer. 
 *
 * Following methods can be implemented: _postUpdate, _postInsert, _postDelete
 * @package Core_Model
 * @subpackage Core_Model_Observer
 */
class Core_Model_Observer_Sessiongcal extends TA_Model_Acl_Abstract implements TA_Model_Observer_Interface
{

	private function _informSubscribers(TA_Model_Observed_Interface $subject, $msg)
	{
		$googleEvent = new Core_Service_GoogleEvent();
	}

	/**
	 * Add session to Google Calendar and update session table with
	 * resulting google calendar id.
	 *
	 * Weird. I have to refresh $subject in order for save() to work!
	 *
	 * @return void
	 */
	public function _postInsert(TA_Model_Observed_Interface $subject, $msg)
	{
		$sessionModel = new Core_Model_Session();
		$subject->refresh();

		$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');

		try {
			$googleEvent = new Core_Service_GoogleEvent();

			$subject->gcal_event_id = $googleEvent->insert(
				$sessionModel->getAllSessionDataById($subject->session_id)->toArray()
			);

			// detach the observer otherwise _update gets fired right after this
			$subject::detachStaticObserver($this);
			$subject->save();
		} catch (Exception $e) {
			// Intercept exception so we can fail without halting the application
			$log = Zend_Registry::get('log');
			$log->emerg($e);
			return $flash->addMessage('Something went wrong updating Google Calendar');
		}

		$flash->addMessage('Successfully added session to Google Calendar');
	}

	/**
	 * Update session in Google Calendar
	 *
	 * @return void
	 */
	public function _postUpdate(TA_Model_Observed_Interface $subject, $msg)
	{
		$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
		try {
			$googleEvent = new Core_Service_GoogleEvent();

			$sessionModel = new Core_Model_Session();
			$session = $sessionModel->getAllSessionDataById($subject->session_id);

			$googleEvent->update($session->toArray());
		} catch (Exception $e) {
			$log = Zend_Registry::get('log');
			$log->emerg($e);
			return $flash->addMessage('Something went wrong updating Google Calendar');
		}

		$flash->addMessage('Successfully updated session in Google Calendar');
	}

	/**
	 * Delete session from Google Calendar
	 *
	 * @return void
	 */
	public function _postDelete(TA_Model_Observed_Interface $subject, $msg)
	{
		$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');

		try {
			$googleEvent = new Core_Service_GoogleEvent();
			$googleEvent->delete($subject->toArray());
		} catch (Exception $e) {
			$log = Zend_Registry::get('log');
			$log->emerg($e);
			return $flash->addMessage('Something went wrong updating Google Calendar');
		}

		$flash->addMessage('Successfully removed session from Google Calendar');

	}

}
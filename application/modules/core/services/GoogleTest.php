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
 * @revision   $Id: GoogleTest.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */

/**
 * Class to manage events stored in personal google calendars
 *
 * @todo this class should eventually be merged with GoogleEvent.php
 * since they basically do the same things
 * @package Core_Service
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 * @see http://framework.zend.com/manual/en/zend.gdata.calendar.html
 */
class Core_Service_GoogleTest {

	private $_sessionNs;

	protected $_client;

	private $_request = 'Zend_Controller_Request_Http';

	/**
	 * Set up init properties
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->_sessionNs = new Zend_Session_Namespace('GoogleAuthSub');
		$this->_request = Zend_Controller_Front::getInstance()->getRequest();
		$this->processPageLoad();
	}

	/**
	 * Returns the URL which the user must visit to authenticate
	 *
	 * @return void
	 */
	protected function _getAuthSubUrl()
	{
		$next = 'https://'. $this->_request->getHttpHost() . $this->_request->getRequestUri();
		$scope = 'https://www.google.com/calendar/feeds/';
		$secure = true;
		$session = true;
		return Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure, $session);
	}

	/**
	 * Returns a HTTP client object with the appropriate headers for communicating
	 * with Google using AuthSub authentication.
	 *
	 * @return Zend_Http_Client
	 */
	protected function _getAuthSubHttpClient()
	{
		$client = new Zend_Gdata_HttpClient();
		// This sets your private key to be used to sign subsequent requests
		$client->setAuthSubPrivateKeyFile('/pub/www/tnc2012/trunk/application/configs/core.key', null, true);
		return $client;
	}

	/**
	 * Processes the workflow to authenticate users.
	 *
	 * Uses the _sessionNs session namespace to store the AuthSub session token
	 * after it is obtained. The single use token supplied in the URL when
	 * redirected after the user succesfully authenticated to Google is
	 * retrieved from the token request parameter
	 *
	 * @return void
	 */
	public function processPageLoad()
	{
		$this->_sessionNs = new Zend_Session_Namespace('GoogleAuthSub');
		$token = $this->_request->getParam('token');

		if ( !$this->_sessionNs->sessionToken ) {
			if (!$token) {
				$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
				$redirector->gotoUrl( $this->_getAuthSubUrl() );
			} elseif ($token) {
				$this->_client = $this->_getAuthSubHttpClient();
				$this->_sessionNs->sessionToken = Zend_Gdata_AuthSub::getAuthSubSessionToken(
					$token, $this->_client
				);
				$this->_client->setAuthSubToken($this->_sessionNs->sessionToken);
			}
		} else {
			$this->_client = $this->_getAuthSubHttpClient();
			$this->_client->setAuthSubToken($this->_sessionNs->sessionToken);
		}
	}

	/**
	 * Helper method to transform object values to Google Event values
	 *
	 * @param	$values		TA_Model_Resource_Db_Table_Row_Abstract
	 * @rerurn	array
	 */
	protected function _modifyValues(TA_Model_Resource_Db_Table_Row_Abstract $row)
	{
		$values = $row->toArray();
		$return = array();

		if ($row instanceof Core_Resource_Event_Item || $row instanceof Core_Resource_Session_Item) {
			$return['title'] = $values['title'];
			$return['description'] = $values['description'];
			$return['start'] = $values['tstart'];
			$return['end'] = $values['tend'];
			$return['location'] = $values['location_address'];
		}
		return $return;
	}

	/**
	 * Creates an event on the authenticated users default calendar with the
	 * specified event details.
	 *
	 * @return string The id URL for the event.
	 */
	public function createEvent(TA_Model_Resource_Db_Table_Row_Abstract $values)
	{
		$values = $this->_modifyValues($values);

		$gc = new Zend_Gdata_Calendar($this->_client);
		$newEntry = $gc->newEventEntry();

		// set event properties
		$newEntry->title = $gc->newTitle(trim($values['title']));
		$newEntry->where = array($gc->newWhere($values['location']));

		$newEntry->content = $gc->newContent($values['description']);
		$newEntry->content->type = 'text';

		$when = $gc->newWhen();

		$start = date_create($values['start']);
		$end = date_create($values['end']);
		$when->startTime = date_format($start, 'Y-m-d\\TH:i:s.000P');
		$when->endTime = date_format($end, 'Y-m-d\\TH:i:s.000P');

		$newEntry->when = array($when);

		$createdEntry = $gc->insertEvent($newEntry);
		return $createdEntry->id->text;
	}





}
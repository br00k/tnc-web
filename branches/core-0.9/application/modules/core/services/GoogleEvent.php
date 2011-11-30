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
 * @revision   $Id: GoogleEvent.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */

/**
 * Class to manage events stored in google calendar
 *
 * @package		Core_Service
 * @author		Christian Gijtenbeek <gijtenbeek@terena.org>
 * @see 		http://framework.zend.com/manual/en/zend.gdata.calendar.html
 */
class Core_Service_GoogleEvent {

	/**
	 * DAO Values
	 * @var array
	 */
	protected $_values;

	/**
	 * Google Calender Service
	 * @var object
	 */
	protected $_service;

	/**
	 * Google Calendar Feed
	 * @var string
	 */
	protected $_url;

	/**
	 * Google Calendar Event Entry
	 * @var object
	 */
	protected $_event;

	/**
	 * Conference config
	 * @var array or Zend_Config object
	 */
	protected $_config;

	public function __construct($config = false)
	{
		if (!$config) {
			$this->_config = Zend_Registry::get('conference');
		}
		$this->_initService();
	}

	/**
	 * Set Google Calendar feed url based on parameter
	 *
	 */
	protected function _setFeedUrl()
	{
		$this->_url = $this->_config['gcal_url'];
	}

	/**
	 * Initialize Google Client service
	 * use ClientLogin to authenticate to Google
	 *
	 * @return void
	 */
	protected function _initService()
	{
		$username = $this->_config['gcal_username'];
		$password = $this->_config['gcal_password'];

		$serviceName = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
		$appName = 'CORE';
		$loginToken   = (isset($_GET['token'])) ? $_GET['token'] : null;
		$loginAnswer = (isset($_GET['answer'])) ? $_GET['answer'] : null;

		try {
			$client = Zend_Gdata_ClientLogin::getHttpClient(
				$username,
				$password,
				$serviceName,
				null,
				$appName,
				$loginToken,
				$loginAnswer
		   	);

		} catch (Zend_Gdata_App_CaptchaRequiredException $e) {
      		echo 'Google requires you to enter this CAPTCHA image <br />';
      		echo '<img src="'.$e->getCaptchaUrl().'" /><br />';
      		echo '<form action="/core/session/captcha" method="GET">';
      		echo 'Answer : <input type="text" name="answer" size="10" />';
      		echo '<input type="hidden" name="token" value="'.$e->getCaptchaToken().'" />';
      		echo '<input type="submit" />';
      		echo '</form>';
      		exit;

		} catch (Zend_Gdata_App_AuthException $e) {
			throw new Exception($e);
		}

		$this->_service = new Zend_Gdata_Calendar($client);
		$this->_setFeedUrl();
	}

	/**
	 * Set values of Data array to google event
	 *
	 * @param Zend_Gdata_Calendar_EventEntry Google Event
	 * @return Zend_Gdata_Calendar_EventEntry Google Event
	 *
		["session_id"] => int(192)
		["title"] => string(5) "test2"
		["description"] => string(0) ""
		["updated"] => NULL
		["logo"] => NULL
		["tag_id"] => NULL
		["location_id"] => int(6)
		["timeslot_id"] => int(3)
		["gcal_event_id"] => NULL
		["conference_id"] => int(1)
		["location_name"] => string(4) "ALFA"
		["location_abbreviation"] => string(1) "A"
		["tstart"] => string(22) "2011-05-16 09:00:00+02"
		["tend"] => string(22) "2011-05-16 10:30:00+02"
	 */
	protected function _transformValues(Zend_Gdata_Calendar_EventEntry $event)
	{
		$event->title = $this->_service->newTitle($this->_values['title']);

		// address stuff
		$event->where = array($this->_service->newWhere($this->_values['location_address']));

	    # Event content
	    $content = $this->_values['description'];

		$link = 'http://tnc2011.terena.org/core/session/'.$this->_values['session_id'];
        $content.=  "<br /><a href='$link'>More information about this event<a/>";

		$event->content = $this->_service->newContent($content);

		# Date/time
		$when = $this->_service->newWhen();

		$start = date_create($this->_values['tstart']);
		$end   = date_create($this->_values['tend']);

		if( date_format($start, 'H:i:s') == '00:00:00' && date_format($end, 'H:i:s') == '00:00:00' ) {
			# All day events (times set to 00:00:00)
		    # See http://code.google.com/apis/calendar/faq.html#all_day_event
            $when->startTime = date_format($start, 'Y-m-d');
            $when->endTime   =   date_format($end, 'Y-m-d');

		} else {
			# regular events
			$when->startTime = date_format($start, 'Y-m-d\\TH:i:s.000P');
			$when->endTime   =   date_format($end, 'Y-m-d\\TH:i:s.000P');
		}
		$event->when = array($when);

		// add extended (hibbum) property for sync purposes
		$extProp = $this->_service->newExtendedProperty('session_id', $this->_values['session_id']);
		$extProps = array_merge($event->extendedProperty, array($extProp));
		$event->extendedProperty = $extProps;

		return $event;
	}


	/**
	 * Return a Google Event based on url id
	 *
	 * @param url
	 * @return object Zend_Gdata_Calendar_EventEntry
	 */
	private function _getOneEvent($url)
	{
		try {
		    return $this->_service->getCalendarEventEntry($url);
		} catch (Zend_Gdata_App_Exception $e) {
		    echo "Error: " . $e->getMessage();
		}
	}

	/**
	 * Insert Google Event
	 *
	 * @param	array	$values
	 * @return	string	google event identifier
	 */
	public function insert($values)
	{
		$this->_values = $values;
		$event = $this->_transformValues(
			$this->_service->newEventEntry()
		);

		$this->_event = $this->_service->insertEvent($event, $this->_url);
		return $this->_event->id->text;
	}

	/**
	 * Update Google Event
	 *
	 * @param	array	$values
	 * @return 	string 	google event identifier
	 */
	public function update($values)
	{
		$this->_values = $values;
		if (!isset($this->_values['gcal_event_id'])) {
			throw new Exception('gcal_event_id not found in record');
		}
		$event = $this->_transformValues(
		    $this->_getOneEvent($this->_values['gcal_event_id'])
		);
		$event->save();
		return $event->id->text;
	}

	/**
	 * Delete Google Event
	 *
	 * @return mixed error message or true on success
	 */
	public function delete($values)
	{
		$this->_values = $values;
		$event = $this->_getOneEvent($this->_values['gcal_event_id']);

		return $event->delete();
	}

	/**
	 * Insert Google Events with batch request
	 *
	 * @param	array	$values
	 * @return	array	Batch response data combined with general event information
	 */
	public function insertBatch($values)
	{
		$entryArray = array();

		foreach ($values as $k => $v) {
			$this->_values = $v;
			$event = $this->_transformValues(
				$this->_service->newEventEntry()
			);
			$event = $this->_addBatchProperties($event, $k+1, 'insert');
			$entryArray[] = $event;
		}

		// perform actual request
		$responseFeed = $this->_performBatchRequest($entryArray);

		// deal with google response
		$return = false;
		foreach ($responseFeed as $responseEntry) {
			$batchResponseData =  $this->_getBatchResponseData($responseEntry);

			// get session_id extended property
			foreach ($responseEntry->getExtendedProperty() as $extProp) {
				if ($extProp->getName() == 'session_id') {
					$sessionId = $extProp->getValue();
				}
			}

			$return[] = array_merge(array(
				'session_id' => $sessionId,
				'title' => $responseEntry->getTitle()->getText()
			), $batchResponseData);
		}

		return $return;
	}

	/**
	 * Adds an extended property to the event specified as a parameter.
	 * An extended property is an arbitrary name/value pair that can be added
	 * to an event and retrieved via the API.  It is not accessible from the
	 * calendar web interface.
	 *
	 * @param  string           $eventId The event ID string
	 * @param  string           $name    The name of the extended property
	 * @param  string           $value   The value of the extended property
	 * @return Zend_Gdata_Calendar_EventEntry|null The updated entry
	 */
	private function _addExtendedProperty($eventId, $name='http://www.example.com/schemas/2005#mycal.id', $value='1234')
	{
		if ($event = $this->_getEvent($eventId)) {
			$extProp = $gc->newExtendedProperty($name, $value);
			$extProps = array_merge($event->extendedProperty, array($extProp));
			$event->extendedProperty = $extProps;
			$eventNew = $event->save();
			return $eventNew;
		} else {
			return null;
		}
	}

	/**
	 * Helper method for batch requests
	 *
	 */
	private function _addBatchProperties($entry, $id, $operation)
	{
	    $extElementId1 = new Zend_Gdata_App_Extension_Element('id', 'batch', 'http://schemas.google.com/gdata/batch', $id);
	    $extElementOp1 = new Zend_Gdata_App_Extension_Element('operation', 'batch', 'http://schemas.google.com/gdata/batch');
	    $extElementOp1->setExtensionAttributes(array(array('namespaceUri' => 'http://schemas.google.com/gdata/batch', 'name' => 'type', 'value' => $operation)));
	    $entry->setExtensionElements(array($extElementId1, $extElementOp1));
	    return $entry;
	}

	/**
	 * Helper method for batch requests
	 *
	 */
	private function _performBatchRequest($entries, $feedUrl = 'http://www.google.com/calendar/feeds/default/private/full/batch')
	{
	    $eventFeed = new Zend_Gdata_Calendar_EventFeed();
	    $eventFeed->setEntry($entries);

	    $response = $this->_service->post($eventFeed->saveXML(), $feedUrl);
	    $responseString = $response->getBody();

	    $responseFeed = new Zend_Gdata_Calendar_EventFeed($responseString);

	    foreach ($responseFeed as $responseEntry) {
	        $responseEntry->setHttpClient($this->_service->getHttpClient());
	    }

	    return $responseFeed;
	}

	/**
	 * Helper method for batch requests
	 *
	 */
	private function _getBatchResponseData($entry)
	{
	    $batchId = null;
	    $batchOperation= null;
	    $batchStatusCode = null;
	    $batchStatusReason = null;
	   	$batchUid = null;

	    $batchNs = 'http://schemas.google.com/gdata/batch';
	    $batchIdElement = $batchNs . ':' . 'id';
	    $batchOperationElement = $batchNs . ':' . 'operation';
	    $batchStatusElement = $batchNs . ':' . 'status';
	    $batchUidElement = 'http://schemas.google.com/gCal/2005' . ':' . 'uid';

	    $extensionElements = $entry->getExtensionElements();

	    foreach ($extensionElements as $extensionElement) {
	        $fullName = $extensionElement->rootNamespaceURI . ':' . $extensionElement->rootElement;
	        switch ($fullName) {
	            case $batchIdElement:
	                $batchId = $extensionElement->getText();
	                break;
	            case $batchOperationElement:
	                $extAttrs = $extensionElement->getExtensionAttributes();
	                $batchOperation = $extAttrs['type']['value'];
	                break;
	            case $batchUidElement:
	                $extAttrs = $extensionElement->getExtensionAttributes();
	                $batchUid = $extAttrs['value']['value'];
	                break;
	            case $batchStatusElement:
	                $extAttrs = $extensionElement->getExtensionAttributes();
	                $batchStatusCode = $extAttrs['code']['value'];
	                $batchStatusReason = $extAttrs['reason']['value'];
	                break;
	        }
	    }

	    return array(
	    	'_operation' => $batchOperation,
	    	'_statusCode' => $batchStatusCode,
	    	'_statusReason' => $batchStatusReason,
	    	'gcal_event_id' => $this->_url . '/' . strstr($batchUid, '@', true)
	    );
	}

}

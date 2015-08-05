<?php
/**
 * Initialize a conference. Get all conference data and store it in APC cache
 * which has unlimited lifetime and is cleared when the conference is edited
 */
class Application_Plugin_ConferenceInit extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$cache = $bootstrap->getResource('cachemanager')
						   ->getCache('apc');

		$hostname = getenv('HTTP_HOST');
		if( ($result = $cache->load('conference'.md5($hostname))) === false ) {
			$db = $bootstrap->getResource('db');

			$model = new Core_Model_Conference();
			$result = $model->getConferenceByHostname($hostname)->toMagicArray();
			$times = $db->fetchRow(
				'select min(tstart) as start, max(tend) as end from timeslots where conference_id='
				.$result['conference_id']
			);
			$times = array_map(function($val) {
				return new Zend_Date($val, Zend_Date::ISO_8601);
			}, $times);

			$result = array_merge($result, $times);

			if (!empty($result)) {
				$cache->save($result, 'conference'.md5($hostname));
			}
		}

		if (empty($result)) {
			exit;
		}

		Zend_Registry::set('conference', $cache->load('conference'.md5($hostname)));

	}

}
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
 * @revision   $Id$
 */

/**
 * Initialize a conference. Get all conference data and store it in APC cache
 * which has unlimited lifetime and is cleared when the conference is edited
 *
 * @package Application_Plugin 
 * @see Core_ConferenceController
 */
class Application_Plugin_ConferenceInit extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$cache = $bootstrap->getResource('cachemanager')
						   ->getCache('apc');

		$hostname = getenv('HTTP_HOST');

		$cache->clean();

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

		// set timezone for this conference
		// @todo: enable when PostgreSQL plays nice
		//if (isset($result['timezone'])) {
		//	date_default_timezone_set($result['timezone']);
		//}

		Zend_Registry::set('conference', $cache->load('conference'.md5($hostname)));

	}

}
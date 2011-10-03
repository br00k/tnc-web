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
 * @revision   $Id: IndexController.php 618 2011-09-29 11:18:54Z visser $
 */
require_once APPLICATION_PATH.'/modules/webdemo/controllers/AbstractController.php';

class Webdemo_IndexController extends Webdemo_AbstractController
{

	public function init()
	{
    	$this->view->messages = $this->_helper->flashMessenger->getMessages();
    	$this->view->threeColumnLayout = true;
		$this->_helper->getHelper('AjaxContext')
					  ->addActionContext('announcement', 'json')
					  ->initContext();
	}

    public function indexAction()
    {
    	$this->view->threeColumnLayout = false;
		$this->view->stylesheet('home.css');

		$schedule = new Core_Model_Schedule();
		#$this->view->roomsessions = $schedule->getStreamData();
		
		$sessions = $schedule->getResource('sessionsview')->getSessionsBeforeDate();
		$this->view->roomsessions = $sessions->group('location');
		
		// uncomment for testing
		//$datearray = array(
		//	'year' => 2011,
		//	'month' => 5,
		//	'day' => 18,
		//	'hour' => 9,
		//	'minute' => 1,
		//	'second' => 10);
    	//$zd = new Zend_Date($datearray);		
		//$this->view->roomsessions = $schedule->getStreamData($zd);		
	}

	public function contactsAction()
	{
		$this->view->stylesheet('contacts.css');
	}

	public function sponsorsAction()
	{
		$this->view->stylesheet('sponsors.css');
	}

	public function announcementAction()
	{
		$db = Zend_Db::factory(
			Zend_Registry::get('webConfig')->resources->multidb->webdb
		);

		$this->view->announcements = $db->fetchAll(
			"SELECT * FROM announcements WHERE active=true ORDER BY created_at DESC"
		);
	}

}
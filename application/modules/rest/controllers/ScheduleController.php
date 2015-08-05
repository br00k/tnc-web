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
 * @revision   $Id: ScheduleController.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */

require_once APPLICATION_PATH.'/modules/rest/controllers/AbstractController.php';

/**
 * ScheduleController
 *
 * @package Rest_Controller
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Rest_ScheduleController extends Rest_AbstractController
{   
	// enable for all actions in this controller the
	// views to be loaded from rest/views/scripts/_shared/<action>.<extension> 
	protected $_sharedViews = true;
	
    public function getAction()
    {
    	/* 
    	 * Example of using a Model/Service and outputting the data
    	 */
    	/*
    	$sessions = new Terena_Session_Model();
    	$list = $sessions->getRecentlyModified();
    	$output = array();
    	foreach ($list as $session) {
			$output[] = $session->getTitle();
    	}
    	*/
    	#$model = new Application_Model_Shared();
    	#$localModule = new Rest_Model_Local();
    	
    	#$output[] = 'Title';
    	#$output[] = $model->getName();
    	#$output[] = $localModule->getId();
    	#$this->view->output = $output;
    	   
    	$scheduleModel = new Core_Model_Schedule();
 		$conference = Zend_Registry::get('conference');
		$view = $this->_getParam('view', 'titles');
		$day = ($this->_getParam('day')) ? urldecode($this->_getParam('day')) : $conference['start']->get('dd/MM/YYYY');

		$timeslot = $this->_getParam('t', null);
		$location = $this->_getParam('l', null);
		$personal = $this->_getParam('personal', false);

		$output = $scheduleModel->getSchedule(null, array('view' => $view, 'day' => $day, 'personal' => $personal));   	

		   	
    	$this->view->output = $output;
    }
    
    public function postAction()
    {
    	// ...
    	$this->getResponse()->setHttpResponseCode(201);
    }
    
    public function deleteAction()
    {
    	// ...
    	$this->getResponse()->setHttpResponseCode(204);
    }

    public function putAction()
    {
    	// ...
    	$this->getResponse()->setHttpResponseCode(201);
    }
    
}

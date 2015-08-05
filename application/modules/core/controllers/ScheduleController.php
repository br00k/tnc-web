<?php

class Core_ScheduleController extends Zend_Controller_Action
{

	public function init()
	{
		$this->_scheduleModel = new Core_Model_Schedule();
		$this->view->headScript()->appendFile('/js/subscribe.js');
		$this->view->messages = $this->_helper->flashMessenger->getMessages();

		if ($this->_scheduleModel->checkAcl('moveSession')) {
			$this->view->headScript()->appendFile('/js/move-session.js');
		}
		$this->view->threeColumnLayout = true;
	}

	public function indexAction()
	{
		return $this->_forward('list');
	}

	/**
	 * Show HTML grid of schedule
	 *
	 */
	public function listAction()
	{
		$this->view->Stylesheet('schedule.css');

		Zend_Controller_Action_HelperBroker::addHelper(new TA_Controller_Action_Helper_ConferenceInfo());

		if ($day = $this->_getParam('day')) {
			$day = urldecode($day);
		} elseif ($date = $this->_helper->conferenceInfo()->isLive()) {
			$day = $date->get('dd/MM/YYYY');
		} else {
			$day = $this->_helper->conferenceInfo('start')->get('dd/MM/YYYY');
		}

		// if conference is live and feedback code exists set view variables
		//if ($this->_helper->conferenceInfo()->isLive() ) {
			$feedbackModel = new Core_Model_Feedback();
			if ($feedbackModel->getFeedbackId()) {
				$this->view->feedbackid = true;
				$this->view->feedback = $this->_getParam('f', false);
			}
		//}

		$view = $this->_getParam('view', 'titles');
		$timeslot = $this->_getParam('t', null);
		$location = $this->_getParam('l', null);

		$this->view->personal = $personal = $this->_getParam('personal', false);
		$this->view->schedule = $this->_scheduleModel->getSchedule(null, array('view' => $view, 'day' => $day, 'personal' => $personal));
		$this->view->days = $this->_scheduleModel->getDays();
		$this->view->timeslots = $this->_scheduleModel->getTimeslots();

		$eventModel = new Core_Model_Event();
		$events = $eventModel->getEvents(null, array('tstart', 'asc'), 'day');
		$this->view->events = $events['rows'];

		$sessionModel = new Core_Model_Session();
		$this->view->subscriptions = $sessionModel->getSubscriptions();

		$this->view->filters = array(
			'view' => $view,
			'day' => $day,
			'personal' => $personal,
			'timeslot' => $timeslot,
			'location' => $location
		);

		if ($this->_getParam('size')) {
			$this->_helper->layout->assign('customlayout', true);
			$this->_helper->layout->setLayout('tnc2011/fullschedule');
		}
	}

}
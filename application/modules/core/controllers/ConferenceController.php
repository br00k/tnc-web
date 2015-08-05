<?php

class Core_ConferenceController extends Zend_Controller_Action implements Zend_Acl_Resource_Interface
{

	private $_conferenceModel;

	public function init()
	{
		$this->_conferenceModel = new Core_Model_Conference();
		$this->view->Stylesheet('advform.css');

		$this->view->messages = $this->_helper->flashMessenger->getMessages();

		// Set navigation to active for all actions within this controller
		$page = $this->view->navigation()->findOneByController( $this->getRequest()->getControllerName() );
		if ($page) {
			$page->setActive();
		}
		$this->view->threeColumnLayout = true;
	}
	
	// @todo move this to parent class? In order to do this I have to extend Zend_Controller_Action
	private function _includeJquery()
	{
		$this->view->headScript()->appendFile('/js/jquery-ui/js/jquery-ui.min.js');
		$this->view->headLink()->appendStylesheet('/js/jquery-ui/css/ui-lightness/jquery-ui.css');
	}

	public function createslotsAction()
	{
		$conferenceId = $this->getRequest()->getParam('id', null);
		if (!$conferenceId) {
			throw new Exception('conference id required');
		}

		$this->_conferenceModel->createTimeslots($conferenceId);
		return $this->_helper->lastRequest();
	}

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function getResourceId()
	{
		return 'Conference';
	}

	public function indexAction()
	{
		return $this->_forward('list');
	}

	public function listAction()
	{
		$this->view->grid = $this->_conferenceModel->getConferences(
			$this->_getParam('page', 1),
			array($this->_getParam('order', null), $this->_getParam('dir', 'asc'))
		);

		$this->view->grid['params']['order'] = $this->_getParam('order');
		$this->view->grid['params']['dir'] = $this->_getParam('dir');
		$this->view->grid['params']['controller'] = $this->getRequest()->getControllerName();
		// assign model to view variable, then from the view I can query the ACL
		$this->view->model = $this->_conferenceModel;
	}

	private function displayForm()
	{
		$this->view->conferenceForm = $this->_conferenceModel->getForm('conference');
		return $this->render('formNew');
	}

	private function displayTimeslotsForm($id)
	{
		$this->view->timeslotsForm = $this->_conferenceModel->getForm('conferenceTimeslots');
		// populate form with defaults
		$this->view->timeslotsForm->setDefaults(array(
			'conference_id' => $id,
			'timeslots' => $this->_conferenceModel->getTimeslots(false, $id)
		));
		return $this->render('timeslots');
	}

	public function editAction()
	{
		$request = $this->getRequest();
		$this->view->id = (int) ($request->getParam('id')) ? $request->getParam('id') : $request->getParam('conference_id');

		// No post; display form
		if ( !$request->isPost() )  {
			$this->_includeJquery();
			$this->view->headScript()->appendFile('/js/conference.js');
			$this->view->conferenceForm = $this->_conferenceModel->getForm('conferenceEdit');
			// populate form with defaults
			$this->view->conferenceForm->setDefaults(
				$this->_conferenceModel->getConferenceById($this->_getParam('id'))->toMagicArray('dd/MM/yyyy')
			);
			return $this->render('formEdit');
		}

		// try to persist item
		if ( $this->_conferenceModel->saveConference($request->getPost(), 'edit') === false ) {
			$this->view->conferenceForm = $this->_conferenceModel->getForm('conferenceEdit');
			return $this->render('formEdit');
		}

		// everything went OK, clear cache and redirect to list action
		$this->_helper->flashMessenger('Succesfully edited record');
		$this->_helper->cache
					  ->getManager()
					  ->getCache('apc')
					  ->remove('conference'.md5($_SERVER['HTTP_HOST']));
		return $this->_helper->redirector->gotoRoute(array('controller'=>'conference', 'action'=>'list'), 'grid');
	}

	public function deleteAction()
	{
		if ( false === $this->_conferenceModel->delete($this->_getParam('id')) ) {
			throw new Core_Model_Exception('Something went wrong with deleting the user');
		}
		return $this->_helper->redirector->gotoRoute(array('controller'=>'conference', 'action'=>'list'), 'grid');
	}

	public function newAction()
	{
		$request = $this->getRequest();

		// No post; display form
		if ( !$request->isPost() )  {
			return $this->displayForm();
		}

		// try to persist user
		if ( $this->_conferenceModel->saveConference($request->getPost()) === false ) {
			return $this->displayForm();
		}

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Succesfully added new record');
		#return $this->_helper->redirector('list');
		return $this->_helper->redirector->gotoRoute(array('controller'=>'conference', 'action'=>'list'), 'grid');
	}

	public function timeslotsAction()
	{
		$this->_includeJquery();
		#$this->view->headScript()->appendFile('/js/jquery-ui-timepicker-addon.min.js');
		$this->view->headScript()->appendFile('/js/timeslots.js');

		$request = $this->getRequest();

		$this->view->id = $id = (int) ($request->getParam('id')) ? $request->getParam('id') : $request->getParam('conference_id');

		// @todo if no $id is supplied throw Exception. maybe require this in Routing

		// No post; display form
		if ( !$request->isPost() )  {
			return $this->displayTimeslotsForm($id);
		}

		// persist timeslots
		if ( $this->_conferenceModel->saveTimeslots($request->getPost()) === false ) {
			$this->view->timeslotsForm = $this->_conferenceModel->getForm('conferenceTimeslots');
			return $this->render('timeslots');
		}

		// everything went OK
		$this->_helper->flashMessenger('Succesfully saved timeslots');
		return $this->_helper->lastRequest();
	}



}

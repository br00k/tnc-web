<?php

class Core_EventController extends Zend_Controller_Action implements Zend_Acl_Resource_Interface
{

	private $_eventModel;

	public function init()
	{
		$this->_eventModel = new Core_Model_Event();
		$this->view->messages = $this->_helper->flashMessenger->getMessages();

		// Set navigation to active for all actions within this controller
		$page = $this->view->navigation()->findOneByController( $this->getRequest()->getControllerName() );
		if ($page) {
			$page->setActive();
		}
		$this->view->threeColumnLayout = true;

	}

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function getResourceId()
	{
		return 'Event';
	}

	public function indexAction()
	{
		return $this->_forward('list');
	}

	private function _displayForm()
	{
		$this->view->eventForm = $this->_eventModel->getForm('event');
		return $this->render('formNew');
	}

	public function listAction()
	{
		$this->view->grid = $this->_eventModel->getEvents(
			null,
			array($this->_getParam('order', 'title'), $this->_getParam('dir', 'asc')),
			'category'
		);
	}

	public function showAction()
	{
		$this->view->stylesheet('schedule.css');
		$request = $this->getRequest();

		$this->view->id = $id = (int) ($request->getParam('id')) ? $request->getParam('id') : $request->getParam('event_id');

		$this->view->event = $this->_eventModel->getAllEventDataById($id);

		$this->_helper->actionStack('list');
		return $this->render('show');

	}

	public function newAction()
	{
		$this->view->Stylesheet('advform.css');
		$request = $this->getRequest();

		// No post; display form
		if ( !$request->isPost() )  {
			return $this->_displayForm();
		}

		// try to persist user
		if ( $this->_eventModel->saveEvent($request->getPost()) === false ) {
			return $this->_displayForm();
		}

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Succesfully added new record');
		#return $this->_helper->redirector('list');
		return $this->_helper->redirector->gotoRoute(array('controller'=>'event', 'action'=>'list'), 'grid');
	}


	public function editAction()
	{
		$this->view->Stylesheet('advform.css');
		$request = $this->getRequest();

		// No post; display form
		if ( !$request->isPost() )  {
			$this->view->eventForm = $this->_eventModel->getForm('eventEdit');
			// populate form with defaults
			$this->view->eventForm->setDefaults(
				$eventDefaults = $this->_eventModel->getEventById($this->_getParam('id'))->toMagicArray('dd/MM/yyyy HH:mm')
			);

			// if event has an image, add it to the MagicFile form element
			if (isset($eventDefaults['file_id'])) {
				$fileModel = new Core_Model_File();
				$this->view->eventForm->file->setTaFile(
					$fileModel->getFileById($eventDefaults['file_id'])
				);
			}

			return $this->render('formEdit');
		}

		// try to persist item
		if ( $this->_eventModel->saveEvent($request->getPost(), 'edit') === false ) {
			$this->view->eventForm = $this->_eventModel->getForm('eventEdit');
			return $this->render('formEdit');
		}

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Succesfully edited record');
		return $this->_helper->redirector->gotoRoute(array('controller'=>'event', 'action'=>'list'), 'grid');
	}

	public function deleteAction()
	{
		if ( false === $this->_eventModel->delete($this->_getParam('id')) ) {
			throw new Core_Model_Exception('Something went wrong with deleting the event');
		}
		return $this->_helper->redirector->gotoRoute(array('controller'=>'event', 'action'=>'list'), 'grid');
	}

	/**
	 * Export a CORE event to a persons Google Calendar
	 *
	 * @todo test
	 */
	public function exportAction()
	{
		$googleTest = new Core_Service_GoogleTest();
		
		$idUrl = $googleTest->createEvent(
		   $this->_eventModel->getAllEventDataById( $this->_getParam('id') )
		);

		$this->_helper->flashMessenger('Succesfully saved this event to your personal Google calendar');
		return $this->_helper->redirector->gotoRoute(
		   array(
		   	'controller'=>'event',
		   	'action'=>'show',
		   	'id'=>$this->_getParam('id')
		   ), 'oneitem');
	}




}









<?php

class Core_PosterController extends Zend_Controller_Action implements Zend_Acl_Resource_Interface
{

	private $_posterModel;

	public function init()
	{
		$this->_posterModel = new Core_Model_Poster();
		$this->view->Stylesheet('advform.css');

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
		return 'Poster';
	}

	public function indexAction()
	{
		return $this->_forward('list');
	}

	public function listAction()
	{
		$this->view->grid = $this->_posterModel->getPosters(
			null,
			array($this->_getParam('order', null), $this->_getParam('dir', 'asc'))
		);

		$this->view->grid['params']['order'] = $this->_getParam('order');
		$this->view->grid['params']['dir'] = $this->_getParam('dir');
		$this->view->grid['params']['controller'] = $this->getRequest()->getControllerName();
	}

	private function displayForm()
	{
		$this->view->posterForm = $this->_posterModel->getForm('poster');
		return $this->render('formNew');
	}

	/**
	 * Show one presentation based on presentation_id
	 *
	 */
	public function showAction()
	{
		$request = $this->getRequest();

		$this->view->poster_id = $id = (int) ($request->getParam('id')) ? $request->getParam('id') : $request->getParam('poster_id');

		$this->view->poster = $poster = $this->_posterModel->getPosterById($id);

		$this->_helper->actionStack('list');
		return $this->render('show');
	}

	public function editAction()
	{
		$request = $this->getRequest();
		$this->view->id = (int) $request->getParam('id');

		// No post; display form
		if ( !$request->isPost() )  {
			$this->view->posterForm = $this->_posterModel->getForm('posterEdit');
			// populate form with defaults
			$this->view->posterForm->setDefaults(
				$defaults = $this->_posterModel->getPosterById($this->_getParam('id'))->toMagicArray('dd/MM/yyyy')
			);

			// if there is a file, add it to the MagicFile form element
			if (isset($defaults['file_id'])) {
				$fileModel = new Core_Model_File();
				$this->view->posterForm->file->setTaFile(
					$fileModel->getFileById($defaults['file_id'])
				);
			}		

			return $this->render('formEdit');
		}

		// try to persist item
		if ( $this->_posterModel->savePoster($request->getPost(), 'edit') === false ) {
			$this->view->posterForm = $this->_posterModel->getForm('posterEdit');
			return $this->render('formEdit');
		}

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Succesfully edited record');
		return $this->_helper->redirector->gotoRoute(array('controller'=>'poster', 'action'=>'list'), 'grid');
	}

	public function deleteAction()
	{
		if ( false === $this->_posterModel->delete($this->_getParam('id')) ) {
			throw new Core_Model_Exception('Something went wrong with deleting the poster');
		}
		return $this->_helper->redirector->gotoRoute(array('controller'=>'poster', 'action'=>'list'), 'grid');
	}

	public function newAction()
	{
		$request = $this->getRequest();

		// No post; display form
		if ( !$request->isPost() )  {
			$this->view->posterForm = $this->_posterModel->getForm('poster');
			// set default values from request parameters
			$this->view->posterForm->setDefaults(
				$request->getParams()
			);
			return $this->render('formNew');
		}

		// try to persist user
		if ( $this->_posterModel->savePoster($request->getPost()) === false ) {
			return $this->displayForm();
		}

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Succesfully added new record');
		#return $this->_helper->redirector('list');
		return $this->_helper->redirector->gotoRoute(array('controller'=>'poster', 'action'=>'list'), 'grid');
	}

}

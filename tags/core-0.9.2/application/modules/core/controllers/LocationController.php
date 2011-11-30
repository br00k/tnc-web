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
 * LocationController
 *
 * @package Core_Controllers
 */ 
class Core_LocationController extends Zend_Controller_Action implements Zend_Acl_Resource_Interface
{

	private $_locationModel;

	public function init()
	{
		$this->_locationModel = new Core_Model_Location();
		$this->view->Stylesheet('advform.css');

		$this->view->messages = $this->_helper->flashMessenger->getMessages();

		// Set navigation to active for all actions within this controller
		$page = $this->view->navigation()->findOneByController( $this->getRequest()->getControllerName() );
		if ($page) {
			$page->setActive();
		}
		$this->view->threeColumnLayout = true;
		$this->view->headScript()->appendFile('/js/conference.js');
	}

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function getResourceId()
	{
		return 'Location';
	}

	public function indexAction()
	{
		return $this->_forward('list');
	}

	public function listAction()
	{
		$this->view->grid = $this->_locationModel->getLocations(
			$this->_getParam('page', 1),
			array($this->_getParam('order', null), $this->_getParam('dir', 'asc'))
		);

		$this->view->grid['params']['order'] = $this->_getParam('order');
		$this->view->grid['params']['dir'] = $this->_getParam('dir');
		$this->view->grid['params']['controller'] = $this->getRequest()->getControllerName();
		// assign model to view variable, then from the view I can query the ACL
		$this->view->model = $this->_locationModel;
	}

	private function displayForm()
	{
		$this->view->locationForm = $this->_locationModel->getForm('location');
		return $this->render('formNew');
	}

	public function editAction()
	{
		$request = $this->getRequest();
		
		// No post; display form
		if ( !$request->isPost() )  {
			$this->view->locationForm = $this->_locationModel->getForm('locationEdit');
			// populate form with defaults
			$this->view->locationForm->setDefaults(
				$defaults = $this->_locationModel->getLocationById($this->_getParam('id'))
					->toMagicArray('dd/MM/yyyy')
			);

			// if location has a picture, add it to the MagicFile form element
			if (isset($defaults['file_id'])) {
				$fileModel = new Core_Model_File();
				$this->view->locationForm->file->setTaFile(
					$fileModel->getFileById($defaults['file_id'])
				);
			}

			return $this->render('formEdit');
		}

		// try to persist item
		if ( $this->_locationModel->saveLocation($request->getPost(), 'edit') === false ) {
			$this->view->locationForm = $this->_locationModel->getForm('locationEdit');
			return $this->render('formEdit');
		}

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Succesfully edited record');
		return $this->_helper->redirector->gotoRoute(array('controller'=>'location', 'action'=>'list'), 'grid');
	}

	public function deleteAction()
	{
		if ( false === $this->_locationModel->delete($this->_getParam('id')) ) {
			throw new TA_Model_Exception('Something went wrong with deleting the location');
		}
		return $this->_helper->redirector->gotoRoute(array('controller'=>'location', 'action'=>'list'), 'grid');
	}

	public function newAction()
	{
		$request = $this->getRequest();

		// No post; display form
		if ( !$request->isPost() )  {
			$this->view->locationForm = $this->_locationModel->getForm('location');
			// set default values from request parameters
			$this->view->locationForm->setDefaults(
				$request->getParams()
			);
			return $this->render('formNew');
		}

		// try to persist user
		if ( $this->_locationModel->saveLocation($request->getPost()) === false ) {
			return $this->displayForm();
		}

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Succesfully added new record');
		#return $this->_helper->redirector('list');
		return $this->_helper->redirector->gotoRoute(array('controller'=>'location', 'action'=>'list'), 'grid');
	}

}

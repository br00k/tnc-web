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
 * @revision   $Id: topicController.php 104 2013-04-08 11:58:49Z gijtenbeek@terena.org $
 */

/**
 * TopicController
 *
 * @package Core_Controllers
 */ 
class Core_TopicController extends Zend_Controller_Action implements Zend_Acl_Resource_Interface
{

	private $_topicModel;

	public function init()
	{
		$this->_topicModel = new Core_Model_Topic();
		$this->view->Stylesheet('advform.css');
		$this->view->Stylesheet('topic.css');

		$this->view->messages = $this->_helper->flashMessenger->getMessages();

		// Set navigation to active for all actions within this controller
		$page = $this->view->navigation()->findOneByController($this->getRequest()->getControllerName());
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
		return 'Topic';
	}

	public function indexAction()
	{
		return $this->_forward('list');
	}

	public function listAction()
	{		
		$this->view->grid = $this->_topicModel->getTopics(
			null,
			array($this->_getParam('order', null), $this->_getParam('dir', 'asc'))
		);

		$this->view->grid['params']['order'] = $this->_getParam('order');
		$this->view->grid['params']['dir'] = $this->_getParam('dir');
		$this->view->grid['params']['controller'] = $this->getRequest()->getControllerName();
	}
	
	private function displayForm()
	{
		$this->view->topicForm = $this->_topicModel->getForm('topic');
		return $this->render('formNew');
	}

	public function editAction()
	{
		$request = $this->getRequest();
		$this->view->id = (int) $request->getParam('id');

		// No post; display form
		if (!$request->isPost()) {
			$this->view->topicForm = $this->_topicModel->getForm('topicEdit');
			// populate form with defaults
			$this->view->topicForm->setDefaults(
				$defaults = $this->_topicModel->gettopicById($this->_getParam('id'))->toMagicArray('dd/MM/yyyy')
			);

			return $this->render('formEdit');
		}

		// try to persist item
		if ($this->_topicModel->saveTopic($request->getPost(), 'edit') === false) {
			$this->view->topicForm = $this->_topicModel->getForm('topicEdit');
			return $this->render('formEdit');
		}

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Succesfully edited record');
		return $this->_helper->redirector->gotoRoute(array('controller'=>'topic', 'action'=>'list'), 'grid');
	}

	public function deleteAction()
	{
		if (false === $this->_topicModel->delete($this->_getParam('id'))) {
			throw new TA_Model_Exception('Something went wrong with deleting the topic');
		}
		return $this->_helper->redirector->gotoRoute(array('controller'=>'topic', 'action'=>'list'), 'grid');
	}

	public function newAction()
	{
		$request = $this->getRequest();

		// No post; display form
		if (!$request->isPost()) {
			$this->view->topicForm = $this->_topicModel->getForm('topic');
			// set default values from request parameters
			$this->view->topicForm->setDefaults(
				$request->getParams()
			);
			return $this->render('formNew');
		}

		// try to persist topic
		if ($this->_topicModel->savetopic($request->getPost()) === false) {
			return $this->displayForm();
		}

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Succesfully added new record');
		#return $this->_helper->redirector('list');
		return $this->_helper->redirector->gotoRoute(array('controller'=>'topic', 'action'=>'list'), 'grid');
	}

}

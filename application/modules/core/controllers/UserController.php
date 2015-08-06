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
 * @revision   $Id: UserController.php 90 2012-12-10 15:39:29Z gijtenbeek@terena.org $
 */

/**
 * UserController
 *
 * @package Core_Controllers
 */
class Core_UserController extends Zend_Controller_Action implements Zend_Acl_Resource_Interface
{

	private $_userModel;

	public function init()
	{
		$this->_userModel = new Core_Model_User();
		$this->view->Stylesheet('advform.css');
		$this->view->messages = $this->_helper->flashMessenger->getMessages();

		// Set navigation to active for all actions within this controller
		$page = $this->view->navigation()->findOneByController($this->getRequest()->getControllerName());
		if ($page) {
			$page->setActive();
		}

		//$this->_helper->cache(array('speakers'), array('speakersaction'));
		$this->view->threeColumnLayout = true;
	}

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function getResourceId()
	{
		return 'User';
	}

	private function getLoginForm()
	{
		return $this->_userModel->getForm('login');
	}

	private function displayForm()
	{
		$this->view->userForm = $this->_userModel->getForm('user');
		return $this->render('formUserAdd');
	}


	public function indexAction()
	{
		return $this->_forward('list');
	}

	public function listAction()
	{
		$session = new Zend_Session_Namespace('userlist');

		if (!isset($session->filters)) {
			$session->filters = new stdClass();
		}
		
		if ($this->getRequest()->getParam('reset_search')) {
			unset($session->filters);
			unset($session->searchString);
		} else if ($searchString = $this->getRequest()->getParam('search')) {			
			$session->searchString = $searchString;
			if ($userIds = $this->_userModel->searchUser($searchString)) {
				$session->filters->user_id = $userIds;	
			} else {
				unset($session->filters->user_id);			
				$session->searchString = null;
			}
		}
		
		$this->view->searchString = $session->searchString;
		
		$this->view->headScript()->appendFile('/js/jquery-ui/js/jquery-ui.min.js');
		$this->view->headLink()->appendStylesheet('/js/jquery-ui/css/ui-lightness/jquery-ui.css');
		$this->view->headScript()->appendFile('/js/users.js');

		$this->view->grid = $this->_userModel->getUsers(
			$this->_getParam('page', 1),
			array($this->_getParam('order', null), $this->_getParam('dir', 'asc')),
			$session->filters
		);

		$this->view->grid['params']['order'] = $this->_getParam('order');
		$this->view->grid['params']['dir'] = $this->_getParam('dir');
		$this->view->grid['params']['controller'] = $this->getRequest()->getControllerName();
	}

	public function speakerAction()
	{
		$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$cache = $bootstrap->getResource('cachemanager')
						   ->getCache('apc');

		$this->view->stylesheet('schedule.css');
		$this->view->threeColumnLayout = true;

		#if( ($this->view->grid = $cache->load('speakerlist') === false ) ) {
			$this->view->grid = $this->_userModel->getUsersWithRole(
				null,
				array($this->_getParam('order', 'fname'), $this->_getParam('dir', 'asc')),
				'presenter'
			);
			#$cache->save($this->view->grid, 'speakerlist');
		#}
	}

	/**
	 * Sign in
	 *
	 */
	public function loginAction()
	{
		$auth = new Core_Service_Authentication(
			$this->getRequest()->getParam('id', null)
		);

		$config = Zend_Registry::get('config');
		$authresult = $auth->authenticate(array('authsource' => $config->simplesaml->authsource));

		if ($authresult === true) {
			$this->_helper->flashMessenger('Successful login');
			$this->_redirect($this->getRequest()->getParam('redir', '/'));
		} else {
		   // failed login
		   return $this->render('login');
		}

	}

	/**
	 * Show one presentation based on presentation_id
	 *
	 */
	public function showAction()
	{
		$request = $this->getRequest();

		$this->view->user_id = $id = (int) ($request->getParam('id')) ? $request->getParam('id') : $request->getParam('user_id');

		$this->view->user = $user = $this->_userModel->getUserById($id);

		$this->view->sessions = $user->getSessions();
		$this->view->presentations = $user->getPresentations();

		$this->_helper->actionStack('speaker');
		return $this->render('show');
	}


	public function editAction()
	{
		$request = $this->getRequest();

		$this->view->id = $request->getParam('id', $request->getParam('user_id'));
		// No post; display form
		if (!$request->isPost()) {
			$this->view->userForm = $this->_userModel->getForm('userEdit');
			// populate form with defaults
			$this->view->userForm->setDefaults(
				$userDefaults = $this->_userModel->getUserById(
					$this->getRequest()->getParam('id', Zend_Auth::getInstance()->getIdentity()->user_id)
				)->toArray()
			);

			// if user has a picture, add it to the MagicFile form element
			if (isset($userDefaults['file_id'])) {
				$fileModel = new Core_Model_File();
				$this->view->userForm->file->setTaFile(
					$fileModel->getFileById($userDefaults['file_id'])
				);
			}

			return $this->render('formUserEdit');
		}

		// try to save user to database
		if ($this->_userModel->saveUser($request->getPost(), 'edit') === false) {
			$this->view->userForm = $this->_userModel->getForm('userEdit');
			return $this->render('formUserEdit');
		}

		// everything went OK, redirect
		$this->_helper->flashMessenger('Successfully edited record');

		return $this->_helper->redirector->gotoRoute(array(
			'controller' => 'user',
			'action' => 'edit',
			'id' => $this->view->id
		), 'gridactions');
	}

	public function deleteAction()
	{
		if (false === $this->_userModel->delete($this->_getParam('id'))) {
			throw new TA_Model_Exception('Something went wrong with deleting the user');
		}
		return $this->_helper->redirector->gotoRoute(array('controller'=>'user', 'action'=>'list'), 'grid');
	}

	/**
	 * Show/save roles linked to this user
	 *
	 */
	public function rolesAction()
	{
		$request = $this->getRequest();
		$id = $this->view->id = $request->getParam('id', $request->getParam('user_id'));

		// No post; display form
		if (!$request->isPost()) {
			$form = $this->view->userRoleForm = $this->_userModel->getForm('userRole');
			$this->view->roles = $this->_userModel->getUserById($id)->getRoles();
			$form->setDefaults(array(
			   	'user_id' => $id
			));

			return $this->render('roles');
		}

		// persist user/presentation mapping
		if ($this->_userModel->saveRoles($request->getPost()) === false) {
			$this->_helper->lastRequest();
		}

		// everything went OK
		return $this->_helper->lastRequest();
	}

	/**
	 * Remove role from user
	 */
	public function deleteroleAction()
	{
		$this->_userModel->deleteRole($this->_getParam('id'));
		return $this->_helper->lastRequest();
	}

	/**
	 * Add a new account, used when inviting users
	 *
	 */
	public function newAction()
	{
		$request = $this->getRequest();

		// No post; display form
		if (!$request->isPost()) {
			$this->view->userForm = $this->_userModel->getForm('userInvite');
			$this->view->userForm->setDefaults(
				$request->getParams()
			);
			return $this->render('formUserAdd');
		}

		// try to persist user
		if ($this->_userModel->saveUser($request->getPost()) === false) {
			$this->view->userForm = $this->_userModel->getForm('userInvite');
			return $this->render('formUserAdd');
		}

		// send email to invitee
		$post = $request->getPost();
		Zend_Controller_Action_HelperBroker::addHelper(new TA_Controller_Action_Helper_SendEmail());
		$emailHelper = $this->_helper->sendEmail;

		$template = $this->_getTemplate($request->getPost('role_id'));

		$conference = Zend_Registry::get('conference');
		$emailHelper->sendEmail(array(
			'template' => $template,
			'subject' => 'Activate your CORE account',
			'html' => true,
			'to_email' => $post['email'],
			'to_name' => $post['fname'].' '.$post['lname']
		), $post);

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Successfully added new record and send invitation to '.$post['email']);
		return $this->_helper->redirector->gotoRoute(array('controller'=>'user', 'action'=>'list'), 'grid');
	}

	/**
	 * @todo whoa is this really still needed??
	 *
	 */
	private function _getTemplate($roleId)
	{
		if (!$roleId) {
			$roleId = 2;
		}
		return 'user/invite_role_id_'.$roleId;
	}

	public function logoutAction()
	{
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
	}

}
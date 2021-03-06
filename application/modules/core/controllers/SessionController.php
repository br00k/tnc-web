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
 * @revision   $Id: SessionController.php 35 2011-10-13 13:56:04Z gijtenbeek@terena.org $
 */

/**
 * SessionController
 *
 * @package Core_Controllers
 */
class Core_SessionController extends Zend_Controller_Action implements Zend_Acl_Resource_Interface
{

	private $_sessionModel;

	public function init()
	{
		$this->_sessionModel = new Core_Model_Session();
		$this->view->Stylesheet('advform.css');
		$this->view->messages = $this->_helper->flashMessenger->getMessages();

		// Set navigation to active for all actions within this controller
		$page = $this->view->navigation()->findBy('controller', $this->getRequest()->getControllerName());
		if ($page) {
			$page->setActive();

		}
		// three column layout?
		if ($page->threeColumnLayout) {
			$this->view->threeColumnLayout = true;
		}

		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('evaluate', 'html')
					->addActionContext('order', 'json')
					->addActionContext('subscribe', 'json')
					->addActionContext('unsubscribe', 'json')
					->initContext();
	}

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function getResourceId()
	{
		return 'Session';
	}

	public function indexAction()
	{
		return $this->_forward('list');
	}

	/**
	 * Synchronize sessions with Google Calendar
	 *
	 */
	public function syncAction()
	{
		$request = $this->getRequest();
		$ack = $request->getParam('ack');
		$this->view->sessions = $this->_sessionModel->getAllSessionData()->count();

		if ($ack) {
			$googleEvent = new Core_Service_GoogleEvent();

			$this->view->response = $response = $googleEvent->insertBatch(
			   $this->_sessionModel->getAllSessionData()->toArray()
			);

			// persist google uid in resource
			$this->_sessionModel->saveSessions($response);

			$eventlogModel = new Core_Model_Eventlog();
			$eventlogModel->saveEventlog(array(
				'event_type' => __METHOD__,
				'timestamp' => 'now()'
			));
		}
	}

	/**
	 * Redo authentication
	 *
	 */
	public function captchaAction()
	{
		$googleEvent = new Core_Service_GoogleEvent();
	}

	/**
	 * Swap sessions
	 *
	 */
	public function moveAction()
	{
		$request = $this->getRequest();

		$order = $this->_sessionModel->moveSession(
			$request->getParam('movers')
		);
		$this->_helper->lastRequest();
	}

	/**
	 * Evaluate a session, deals with new and existing evaluations
	 *
	 */
	public function evaluateAction()
	{
		$request = $this->getRequest();
		$id = (int) ($request->getParam('id')) ? $request->getParam('id') : $request->getParam('session_id');

		$this->view->evaluationForm = $this->_sessionModel->getForm('sessionEvaluation');
		$defaults = $this->_sessionModel->getEvaluationBySessionId($id);
		if ($defaults) {
			$this->view->updated = (isset($defaults['updated'])) ? $defaults['updated'] : $defaults['inserted'];
			$userModel = new Core_Model_User();
			$this->view->user = $userModel->getUserById($defaults['user_id']);
		}
		$defaults['session_id'] = $id;

		// No post;
		if (!$request->isPost()) {
			// populate form with defaults
			$this->view->evaluationForm->setDefaults($defaults);
			return $this->render('evaluate');
		}

		// try to persist evaluation
		if ($this->_sessionModel->saveEvaluation($request->getPost()) === false) {
			$this->view->evaluationForm = $this->_sessionModel->getForm('sessionEvaluation');
			return $this->render('evaluate');
		}

	}

	/**
	 * Show one session based on session_id
	 *
	 */
	public function showAction()
	{
		if ($this->_sessionModel->checkAcl('order') || $this->_sessionModel->checkAcl('evaluate')) {
			$this->view->headScript()->appendFile('/js/jquery-ui/js/jquery-ui.min.js');
			$this->view->headLink()->appendStylesheet('/js/jquery-ui/css/ui-lightness/jquery-ui.css');
			$this->view->headScript()->appendFile('/js/sessioneval.js');
			$this->view->headScript()->appendFile('/js/pres-sort.js');
		}

		$this->view->Stylesheet('schedule.css');

		$request = $this->getRequest();

		$id = (int) ($request->getParam('id')) ? $request->getParam('id') : $request->getParam('session_id');

		$this->view->presentationForm = $this->_sessionModel->getForm('sessionPresentation');
		$this->view->session = $session = $this->_sessionModel->getAllSessionDataById($id);

		$conference = Zend_Registry::get('conference');
		if ($this->_sessionModel->checkAcl('evaluate')) {
			// add evaluate action to the stack!
			$this->_helper->actionStack('evaluate');
		}

		// No post;
		if (!$request->isPost()) {
			// populate form with defaults
			$this->view->presentationForm->setDefaults(array(
			   	'session_id' => $id
			));
			return $this->render('show');
		}
		// persist presentation/session mapping
		$save = $this->_sessionModel->savePresentations($request->getPost());

		if ($save === false) {
			$this->_helper->flashMessenger(
				'Something went wrong adding the presentation to this session'
			);
		} elseif ($save instanceof Zend_Db_Table_Row) {
			$this->_helper->flashMessenger(
				'This presentation already belongs to <a href="/core/session/'.$save->session_id.'">another session</a>'
			);
		}

		$this->_helper->lastRequest();
	}

	/**
	 * Swap order of sessions
	 *
	 */
	public function orderAction()
	{
		$request = $this->getRequest();

		$order = $this->_sessionModel->setPresentationOrder(
			$request->getParam('session_id'),
			$request->getParam('order')
		);

		$this->view->messages = '';
		return;
	}

	/**
	 * Delete presentation from session
	 *
	 */
	public function deletepresentationAction()
	{
		$this->_sessionModel->deletePresentation($this->_getParam('id'));
		return $this->_helper->lastRequest();
	}

	/**
	 * Show list of sessions
	 *
	 */
	public function listAction()
	{
		$this->view->grid = $this->_sessionModel->getSessions(
			#$this->_getParam('page', 1),
			null,
			array($this->_getParam('order', null), $this->_getParam('dir', 'asc'))
		);

		$this->view->grid['params']['order'] = $this->_getParam('order');
		$this->view->grid['params']['dir'] = $this->_getParam('dir');
		$this->view->grid['params']['controller'] = $this->getRequest()->getControllerName();
	}

	private function displayForm()
	{
		$this->view->sessionForm = $this->_sessionModel->getForm('session');
		return $this->render('formNew');
	}

	public function editAction()
	{
		$request = $this->getRequest();
		$this->view->id = (int) $request->getParam('id');

		// No post; display form
		if (!$request->isPost()) {
			$this->view->sessionForm = $this->_sessionModel->getForm('sessionEdit');
			// populate form with defaults
			$this->view->sessionForm->setDefaults(
				$this->_sessionModel->getSessionById($this->_getParam('id'))->toArray()
			);
			return $this->render('formEdit');
		}

		// try to save user to database
		if ($this->_sessionModel->saveSession($request->getPost(), 'edit') === false) {
			$this->view->sessionForm = $this->_sessionModel->getForm('sessionEdit');
			return $this->render('formEdit');
		}

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Successfully edited record');
		return $this->_helper->redirector->gotoRoute(array('controller'=>'session', 'action'=>'list'), 'grid');
	}

	public function deleteAction()
	{
		if (false === $this->_sessionModel->delete($this->_getParam('id'))) {
			throw new TA_Model_Exception('Something went wrong with deleting the user');
		}
		return $this->_helper->redirector->gotoRoute(array('controller'=>'session', 'action'=>'list'), 'grid');
	}

	public function newAction()
	{
		$request = $this->getRequest();

		// No post; display form
		if (!$request->isPost()) {
			$this->view->sessionForm = $this->_sessionModel->getForm('session');
			// set default values from request parameters
			$this->view->sessionForm->setDefaults(
				$request->getParams()
			);
			return $this->render('formNew');
		}

		// try to persist user
		if ($this->_sessionModel->saveSession($request->getPost()) === false) {
			return $this->displayForm();
		}

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Successfully added new record');

		return $this->_helper->redirector->gotoRoute(array('controller'=>'session', 'action'=>'list'), 'grid');
	}



	/**
	 * Delete user session link
	 *
	 */
	public function deleteuserlinkAction()
	{
		$this->_sessionModel->deleteChair($this->_getParam('id'));
		return $this->_helper->lastRequest();
	}

	/**
	 * Show/save users linked to this session
	 *
	 */
	public function chairsAction()
	{
		$request = $this->getRequest();
		$this->view->id = (int) $request->getParam('id');
		$this->view->Stylesheet('submit.css');

		// @todo Fix this to use submission_id regardless, have to do this in bootstrap
		$id = (int) ($request->getParam('id')) ? $request->getParam('id') : $request->getParam('session_id');
		$this->view->session = $session = $this->_sessionModel->getSessionById($id);

		// No post; display form
		if (!$request->isPost()) {
			$form = $this->view->sessionUserForm = $this->_sessionModel->getForm('sessionUser');
			// populate form with defaults
			$this->view->sessionUserForm->setDefaults(array(
			   	'session_id' => $id
			));
			$form->getElement('user_id')->setTaRow(
				$this->_sessionModel->getSessionById($id)
			);
			return $this->render('chairs');
		}

		// persist user/session mapping
		if ($this->_sessionModel->saveChairs($request->getPost()) === false) {
			$this->_helper->lastRequest();
		}

		// everything went OK
		return $this->_helper->lastRequest();
	}

	/**
	 * Show/save files linked to this presentation
	 *
	 */
	public function filesAction()
	{
		$request = $this->getRequest();
		$id = (int) ($request->getParam('id')) ? $request->getParam('id') : $request->getParam('session_id');
		$this->view->id = (int) $request->getParam('id');
		$this->view->session = $this->_sessionModel->getSessionById($this->view->id);

		// No post; display form
		if (!$request->isPost()) {
			$form = $this->view->sessionFilesForm = $this->_sessionModel->getForm('sessionFiles');
			$form->setDefaults(array(
			   	'session_id' => $id
			));

			// set linked files to magic file elements
			foreach ($files = $this->_sessionModel->getFiles($id) as $file) {
			   	$form->files->{$file->core_filetype}->setTaFile(
					$file
			   	);
			}

			return $this->render('files');
		}

		// persist user/files
		if ($this->_sessionModel->saveFiles($request->getPost()) === false) {
			$this->_helper->lastRequest();
		}

		// everything went OK
		return $this->_helper->lastRequest();
	}

	/**
	 * Subscribe a user to a session
	 *
	 */
	public function subscribeAction()
	{
		// persist subscription
		$this->_sessionModel->subscribeUser($this->getRequest()->getParam('id'));
	}

	/**
	 * Unsubscribe user from session
	 *
	 */
	 public function unsubscribeAction()
	 {
		// persist unsubscription
		$this->_sessionModel->unsubscribeUser($this->getRequest()->getParam('id'));
	 }

	/**
	 * Export a CORE session to a persons Google Calendar
	 *
	 */
	public function exportAction()
	{
		try {
			$googleTest = new Core_Service_GoogleTest();

			$idUrl = $googleTest->createEvent(
			   $this->_sessionModel->getAllSessionDataById($this->_getParam('id'))
			);
		} catch (Zend_Gdata_App_AuthException $e) {
			$this->_helper->flashMessenger('Something went wrong saving this session to your personal calendar - please contact the admins of this site');
			$log = Zend_Registry::get('log');
			$log->emerg($e);
			$this->_redirectToSession();
		}

		$this->_helper->flashMessenger('Succesfully saved this session to your personal Google calendar');
		$this->_redirectToSession();
	}

	/**
	 * Helper method for exportAction
	 * redirects user to session details page
	 *
	 */
	private function _redirectToSession()
	{
		return $this->_helper->redirector->gotoRoute(
		   array(
		   	'controller'=>'session',
		   	'action'=>'show',
		   	'id'=>$this->_getParam('id')
		   ), 'oneitem');
	}

}

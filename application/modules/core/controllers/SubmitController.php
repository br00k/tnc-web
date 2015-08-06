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
 * @revision   $Id: SubmitController.php 41 2011-11-30 11:06:22Z gijtenbeek@terena.org $
 */

/**
 * SubmitController
 *
 * @package Core_Controllers
 */
class Core_SubmitController extends Zend_Controller_Action implements Zend_Acl_Resource_Interface
{

	private $_submitModel;

	public function init()
	{
		$this->_submitModel = new Core_Model_Submit();
		$this->view->Stylesheet('advform.css');
		$this->view->messages = $this->_helper->flashMessenger->getMessages();

		// @todo: remove this
		if (!Zend_Registry::isRegistered('formconfig')) {
			$formConfig = new Zend_Config(require APPLICATION_PATH.'/configs/formdefaults.php');
			Zend_Registry::set('formconfig', $formConfig);
		}

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
		return 'submission';
	}

	public function indexAction()
	{
		return $this->_forward('new');
	}

	/**
	 * Send email to accepted/rejected papers
	 *
	 * @return void
	 */
	public function mailAction()
	{
		$request = $this->getRequest();
		$id = $request->getParam('id');

		if (false == $submitters = $this->_submitModel->getSubmissionsForMail($id)) {
			return;
		}

		$this->view->submissions = count($submitters);
		$this->view->status = $id;
		$this->view->dummy = $request->getParam('dummy');

		if (!$request->isPost()) {
			$this->view->mailForm = $this->_submitModel->getForm('submitMail');
			$this->view->mailForm->setDefaults(array(
				'id' => $id
			));
			return $this->render();
		}

		$conference = Zend_Registry::get('conference');

		Zend_Controller_Action_HelperBroker::addHelper(new TA_Controller_Action_Helper_SendEmail());
		$emailHelper = $this->_helper->sendEmail;

		$this->view->sent = array();
		$template = ($id == 1) ? 'submit/accept' : 'submit/reject';

		foreach ($submitters as $submit) {
			$emailHelper->sendEmail(array(
				'dummy' => $request->getParam('dummy'),
				'template' => $template,
				'html' => true,
				'subject' => $conference['abbreviation'].':'.$submit['title'],
				'to_email' => $submit['email'],
				'to_name' => $submit['fname'].' '.$submit['lname']
			), $submit);

			$this->view->sent[] = array(
				'email' => $submit['email'],
				'title' => $submit['title']
			);
		}

		if ($this->view->dummy == 0) {
			$eventlogModel = new Core_Model_Eventlog();
			$eventlogModel->saveEventlog(array(
				'event_type' => ($id == 1) ? 'mail_accepted' : 'mail_rejected',
				'timestamp' => 'now()'
			));
		}
	}

	/**
	 * Helper method to get submissions user should review
	 *
	 * @param	boolean	$tiebreak	true to get submissions that assigned user as tiebreaker
	 *								null to get all submissions user should review (including
	 *								tiebreakers)
	 * @return	mixed	Array of submission_id or false
	 */
	private function _personalReviewFilter($tiebreak=null)
	{
	   	if ( $submissions = Zend_Auth::getInstance()->getIdentity()->getSubmissionsToReview() ) {
			if ($tiebreak) {
				return array_keys(
					array_filter($submissions, function($val) use($tiebreak) {
	   					return ($val === $tiebreak);
	   				})
	   			);
	   		}
	   		$reviewModel = new Core_Model_Review();
			return array_keys(
				$reviewModel->getPersonalTiebreakers(null, false, true)
			);
		}
		return false;
	}

	/**
	 * List submissions
	 *
	 */
	public function listAction()
	{
		$session = new Zend_Session_Namespace('gridsubmit');

		if (!isset($session->filters)) {
			$session->filters = new stdClass();
		}

		// set filters, treat submission_id different because this has an array as value
		if ($this->getRequest()->isPost()) {
			$params = $this->getRequest()->getPost();
			foreach ($params as $field => $value) {
				if ($field == 'submission_id') {
					// default value for form element
					$this->view->{$field} = $value;
					if ($value == 1) {
	   					if ( $filter = $this->_personalReviewFilter() ) {
							$session->filters->{$field} = $filter;
						}
					} else {
						unset($session->filters->$field);
					}
				} else {
					if ((int) $value !== 0) {
						$session->filters->$field = $value;
					} else {
						unset($session->filters->$field);
					}
				}
			}
		}

		// set defaults for form elements from session
		foreach ($session->filters as $filter => $value) {
			if ($filter != 'submission_id') {
				$this->view->$filter = $value;
			} else {
				$this->view->submission_id = 1;
			}
		}

		$this->view->grid = $this->_submitModel->getSubmissions(
			null,
			array($this->_getParam('order', null), $this->_getParam('dir', 'asc')),
			$session
		);

		$this->view->grid['params']['order'] = $this->_getParam('order');
		$this->view->grid['params']['dir'] = $this->_getParam('dir');
		$this->view->grid['params']['controller'] = $this->getRequest()->getControllerName();
		// assign model to view variable, then from the view I can query the ACL
		$this->view->model = $this->_submitModel;
	}

	/**
	 * Download submissions
	 *
	 */
	public function downloadAction()
	{
  		$this->_helper->viewRenderer->setNoRender();
		$session = new Zend_Session_Namespace('gridsubmit');

		// if session is set, use that as filter otherwise use 'my submissions to review' as filter
		if (!isset($session->filters)) {
			$session->filters = new stdClass();
			$session->filters->submission_id = array_keys(Zend_Auth::getInstance()->getIdentity()->getSubmissionsToReview());
		}

		$archive = $this->_submitModel->getArchiveBySubmissionIds(
			$session,
			$this->getRequest()->getParam('format', 'zip')
		);

		return $this->_helper->redirector->gotoRoute(array(
			'controller'=>'file',
			'action'=>'getstaticfile',
			'file' => $archive,
			'type' => 'zip'
		));
	}

	private function displayForm()
	{
		$this->view->submitForm = $this->_submitModel->getForm('submit');
		return $this->render('formNew');
	}

	/**
	 * Delete submission
	 *
	 */
	public function deleteAction()
	{
		if (false === $this->_submitModel->delete($this->_getParam('id'))) {
			throw new TA_Model_Exception('Something went wrong with deleting the user');
		}
		return $this->_helper->redirector->gotoRoute(array('controller'=>'submit', 'action'=>'list'), 'grid');
	}

	/**
	 * Delete reviewer submission link
	 *
	 */
	public function deleteuserlinkAction()
	{
		$this->_submitModel->deleteReviewer($this->_getParam('id'));
		return $this->_helper->lastRequest();
	}

	/**
	 * Toggle reviewer tiebreaker
	 *
	 */
	public function toggletiebreakerAction()
	{
		$this->_submitModel->setTiebreaker(
			$this->_getParam('id'),
			$this->_getParam('value', false)
		);
		return $this->_helper->lastRequest();
	}

	// @todo move this to parent class? In order to do this I have to extend Zend_Controller_Action
	private function _includeJquery()
	{
		$this->view->headScript()->appendFile('/js/jquery-ui/js/jquery-ui.min.js');
		$this->view->headLink()->appendStylesheet('/js/jquery-ui/css/ui-lightness/jquery-ui.css');
	}

	/**
	 * Show/save reviewers linked to this submission
	 *
	 */
	public function reviewersAction()
	{
		$request = $this->getRequest();
		$this->view->Stylesheet('submit.css');

		// @todo Fix this to use submission_id regardless
		$id = (int) ($request->getParam('id')) ? $request->getParam('id') : $request->getParam('submission_id');
		$this->view->submission = $submission = $this->_submitModel->getSubmissionById($id);

		// No post; display form
		if (!$request->isPost()) {
			$form = $this->view->submitUserForm = $this->_submitModel->getForm('submitUser');
			// populate form with defaults
			$this->view->submitUserForm->setDefaults(array(
			   	'submission_id' => $id
			));
			$form->getElement('user_id')->setTaRow(
				$this->_submitModel->getSubmissionById($id)
			);
			return $this->render('reviewers');
		}

		// persist user/submission mapping
		if ($this->_submitModel->saveReviewers($request->getPost()) === false) {
			$this->_helper->lastRequest();
		}

		// everything went OK
		$this->_helper->flashMessenger('Succesfully linked reviewer to the submission');
		return $this->_helper->lastRequest();
	}

	/**
	 *
	 *
	 */
	public function editAction()
	{
		$request = $this->getRequest();

		// No post; display form
		if (!$request->isPost()) {
			$this->view->submitForm = $this->_submitModel->getForm('submitEdit');
			// populate form with defaults
			$this->view->submitForm->setDefaults(
				$defaults = $this->_submitModel->getSubmissionById($this->_getParam('id'))->toArray()
			);

			if (isset($defaults['file_id'])) {
				$fileModel = new Core_Model_File();
				// add currently linked file to file input box
				$this->view->submitForm->submission->file->setTaFile(
					$fileModel->getFileById($defaults['file_id'])
				);
			}

			return $this->render('formEdit');
		}

		// try to save submission to database
		if ($this->_submitModel->saveSubmission($request->getPost(), 'edit') === false) {
			$this->view->submitForm = $this->_submitModel->getForm('submitEdit');
			return $this->render('formEdit');
		}

		// everything went OK, redirect
		$this->_helper->flashMessenger('Succesfully edited record');
		if (Zend_Auth::getInstance()->getIdentity()->role != 'admin') {
			// reload session because user details have changed (their submission data)
			$userModel = new Core_Model_User();
			$userModel->getUserById(Zend_Auth::getInstance()->getIdentity()->user_id)->reloadSession();
			return $this->_helper->lastRequest();
		}
		return $this->_helper->redirector->gotoRoute(array('controller'=>'submit', 'action'=>'list'), 'grid');
	}
	
	/**
	 *
	 *
	 */
	public function newAction()
	{
		// @todo: this can't be right, I add the ACL logic here.
		// I can't do it in Core.php because that causes a chicken/egg problem
		// with Zend_Registry::get('conference') not being set.
		// no worries, because the date assertion is void anyway
		$acl = Zend_Registry::get('acl');
		$acl->allow('user', 'Submit', 'new', new Core_Model_Acl_DateAssertion());

		$request = $this->getRequest();

		// No post; display form
		if (!$request->isPost()) {
			$this->view->submitForm = $this->_submitModel->getForm('submit');
			return $this->render('formNew');
		}

		// try to persist data
		if ($this->_submitModel->saveSubmission($request->getPost()) === false) {
			$this->view->error = true;
			return $this->displayForm();
		}

		// send email to submitter
		Zend_Controller_Action_HelperBroker::addHelper(new TA_Controller_Action_Helper_SendEmail());
		$emailHelper = $this->_helper->sendEmail;
		$identity = Zend_Auth::getInstance()->getIdentity();

		$conference = Zend_Registry::get('conference');
		$emailHelper->sendEmail(array(
			'template' => 'submit/new',
			'subject' => 'TNC15 submission',
			'html' => true,
			'to_email' => $identity->email,
			'to_name' => $identity->fname.' '.$identity->lname
		), $request->getPost());

		// everything went OK, redirect
		$this->_helper->flashMessenger('Thank you for your submission, please check your details below');
		if (Zend_Auth::getInstance()->getIdentity()->role != 'admin') {
			// reload session because user details have changed (their submission data)
			$userId = Zend_Auth::getInstance()->getIdentity()->user_id;
			$userModel = new Core_Model_User();
			$userModel->getUserById($userId)->reloadSession();
			#return $this->_helper->lastRequest();
			return $this->_helper->redirector->gotoRoute(array(
				'controller'=>'user', 
				'action'=>'edit', 
				'id' => $userId
			), 'gridactions');			
		}
		return $this->_helper->redirector->gotoRoute(array('controller'=>'submit', 'action'=>'list'), 'grid');
	}

}

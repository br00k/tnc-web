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
 * @revision   $Id: ReviewController.php 83 2012-12-06 14:24:20Z gijtenbeek@terena.org $
 */

/**
 * ReviewController
 *
 * @package Core_Controllers
 */
class Core_ReviewController extends Zend_Controller_Action implements Zend_Acl_Resource_Interface
{

	private $_reviewModel;

	public function init()
	{
		$this->_reviewModel = new Core_Model_Review();
		$this->view->Stylesheet('advform.css');
		$this->view->Stylesheet('submit.css');
		$this->view->messages = $this->_helper->flashMessenger->getMessages();

		if (!Zend_Registry::isRegistered('formconfig')) {
			$formConfig = new Zend_Config(require APPLICATION_PATH.'/configs/formdefaults.php');
			Zend_Registry::set('formconfig', $formConfig);
		}

		// Set navigation to active for all actions within this controller
		$page = $this->view->navigation()->findOneByController($this->getRequest()->getControllerName());
		if ($page) {
			$page->setActive();
		}

		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('list', 'json')
					->initContext();
	}

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function getResourceId()
	{
		return 'review';
	}

	public function indexAction()
	{
		return $this->_forward('list');
	}

	/**
	 * Send email to all reviewers
	 *
	 * @return void
	 */
	public function mailAction()
	{
		$request = $this->getRequest();
		$this->view->reminder = $reminder = $request->getParam('reminder');

		if (false == $reviewers = $this->_reviewModel->getReviewersForMail($reminder)) {
			return;
		}

		$this->view->reviewers = count($reviewers);
		$this->view->dummy = $request->getParam('dummy');
		if (!$request->isPost()) {
			$this->view->mailForm = $this->_reviewModel->getForm('reviewMail');
			if ($reminder) {
				$this->view->mailForm->setAction('/core/review/mail/reminder/1');
			}
			return $this->render();
		}

		$conference = Zend_Registry::get('conference');

		Zend_Controller_Action_HelperBroker::addHelper(new TA_Controller_Action_Helper_SendEmail());
		$emailHelper = $this->_helper->sendEmail;

		$this->view->sent = array();

		$template = ($reminder) ? 'review/massmail-reminder' : 'review/massmail';

		foreach ($reviewers as $review) {
			$emailHelper->sendEmail(array(
				'dummy' => $request->getParam('dummy'),
				'template' => $template,
				'html' => true,
				'subject' => $conference['abbreviation'].' Review',
				'to_email' => $review['email'],
				'to_name' => $review['fname'].' '.$review['lname']
			), $review);

			$this->view->sent[] = array(
				'email' => $review['email']
			);
		}

		if ($this->view->dummy == 0) {
			$eventlogModel = new Core_Model_Eventlog();
			$eventlogModel->saveEventlog(array(
				'event_type' => ($reminder) ? 'mail_reviewers-reminder' : 'mail_reviewers',
				'timestamp' => 'now()'
			));
		}

	}

	/**
	 * List reviews assigned to current user
	 *
	 */
	public function listmineAction()
	{
		$this->view->headScript()->appendFile('/js/reviewtoggler.js');
		$this->view->MySubmissionsToReview = $this->_reviewModel->getPersonalTiebreakers();
		return $this->render('list-personal');
	}

	public function listpersonalAction()
	{	
		$this->view->headScript()->appendFile('/js/reviewtoggler.js');
		if ($this->getRequest()->isPost()) {
			$this->view->user_id = $userId = $this->getRequest()->getParam('user_id');
			$this->view->MySubmissionsToReview = $this->_reviewModel->getPersonalTiebreakers(
				$userId
			);
		} 
		return $this->render('list-personal');
	}

	/**
	 * List of reviews for a submission
	 * This action also deals with managing the session status
	 * (setting the status and 'proposed' session)
	 *
	 */
	public function listAction()
	{
		$request = $this->getRequest();
		$submissionId = ($request->getParam('id')) ? $request->getParam('id') : $request->getParam('submission_id');

		$filter = new stdClass();
		$filter->submission_id = $submissionId;

		$this->view->grid = $this->_reviewModel->getReviews(
			null,
			array($this->_getParam('order', null), $this->_getParam('dir', 'asc')),
			$filter
		);

		$this->view->grid['params']['order'] = $this->_getParam('order');
		$this->view->grid['params']['dir'] = $this->_getParam('dir');
		$this->view->grid['params']['controller'] = $this->getRequest()->getControllerName();
		$this->view->tiebreaker = $this->view->grid['rows']->getTieBreaker();

		$submitModel = new Core_Model_Submit();
		$this->view->submission = $submitModel->getAllSubmissionDataById($submissionId);

		// No post; display 'status' form
		if (!$request->isPost()) {
			$this->view->statusForm = $submitModel->getForm('submitStatus');
			$submitStatus = $submitModel->getStatusBySubmissionId($submissionId);
			// populate form with defaults
			$this->view->statusForm->setDefaults(
				$submitStatus
			);
			return $this->render('list');
		}
		// try to persist submission status
		if ($submitModel->saveStatus($request->getPost()) === false) {
			return $this->render('list');
		}

		// everything went OK, redirect to submission list with relevant anchor name
		$this->_helper->flashMessenger('Submission status saved');
		$url = $this->_helper->getHelper('Url')->url(array('controller'=>'submit', 'action'=>'list'), 'grid');
		return $this->_helper->redirector->gotoUrl($url.'#s'.$submissionId);
	}

	private function displayForm()
	{
		$this->view->reviewForm = $this->_reviewModel->getForm('review');
		// set the value of the hidden form element @todo: can't I do this from the model instead?
		$this->view->reviewForm->getElement('submission_id')->setValue(
			$this->getRequest()->getParam('id')
		);
		return $this->render('formNew');
	}

	/**
	 * Edit a review
	 *
	 */
	public function editAction()
	{
		$request = $this->getRequest();

		$identity = Zend_Auth::getInstance()->getIdentity();

		$reviewId = ($request->getParam('id')) ? $request->getParam('id') : $request->getParam('review_id');

		// get user_id of person who did the review
		$userId = $this->_reviewModel->getReviewById($reviewId)->user_id;

		// users can only edit their own reviews
		if (($userId !== $identity->user_id) && (!$identity->isAdmin())) {
			throw new TA_Model_Acl_Exception("Insufficient rights to edit this review");
		}

		// No post; display form
		if (!$request->isPost()) {
			$this->view->reviewForm = $this->_reviewModel->getForm('reviewEdit');
			$review = $this->_reviewModel->getReviewById($this->_getParam('id'));
			// populate form with defaults
			$this->view->reviewForm->setDefaults(
				$review->toArray()
			);
			$submitModel = new Core_Model_Submit();
			$this->view->submission = $submitModel->getAllSubmissionDataByReview(
				$review
			);

			return $this->render('formEdit');
		}

		// try to save user to database
		if ($this->_reviewModel->saveReview($request->getPost(), 'edit') === false) {
			$this->view->reviewForm = $this->_reviewModel->getForm('reviewEdit');
			return $this->render('formEdit');
		}

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Succesfully edited record');
		return $this->_helper->redirector->gotoRoute(array('controller'=>'submit', 'action'=>'list'), 'grid');
	}

	/**
	 * Delete a review
	 *
	 */
	public function deleteAction()
	{
		if (false === $this->_reviewModel->delete($this->_getParam('id'))) {
			throw new TA_Model_Exception('Something went wrong with deleting the review');
		}
		return $this->_helper->redirector->gotoRoute(array('controller'=>'submit', 'action'=>'list'), 'grid');
	}

	/**
	 * Create a new review
	 *
	 */
	public function newAction()
	{
		$request = $this->getRequest();

		// No post; display form
		if (!$request->isPost()) {
			$submitModel = new Core_Model_Submit();
			$this->view->submission = $submitModel->getAllSubmissionDataById($request->getParam('id'));
			return $this->displayForm();
		}

		// try to persist data
		if ($this->_reviewModel->saveReview($request->getPost()) === false) {
			return $this->displayForm();
		}

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Thank you for your review');
		return $this->_helper->redirector->gotoRoute(array('controller'=>'submit', 'action'=>'list'), 'grid');
	}

}

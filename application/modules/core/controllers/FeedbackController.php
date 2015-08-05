<?php

class Core_FeedbackController extends Zend_Controller_Action
{

	private $_feedbackModel;

	public function init()
	{
		$this->_feedbackModel = new Core_Model_Feedback();
		$this->view->Stylesheet('advform.css');
		$this->view->threeColumnLayout = true;

		$this->view->navigation()->addPages(array(
			array(
			    'label' => '',
			    'title' => '',
			    'module' => 'core',
			    'controller' => 'feedback',
			    'action' => 'index',
			    'route' => 'main-module',
			    'active' => true,
			    'reset_params' => true,
			    'pages' => array(
					array(
					    'label' => 'Participant',
					    'corename' => 'feedback.participant',
					    'title' => 'participant',
					    'module' => 'core',
					    'controller' => 'feedback',
					    'action' => 'participant',
					    'route' => 'main-module',
					    'visible' => true,
					    'reset_params' => true
					),
					array(
					    'label' => 'General',
					    'corename' => 'feedback.general',
					    'title' => 'general',
					    'module' => 'core',
					    'controller' => 'feedback',
					    'action' => 'general',
					    'route' => 'main-module',
					    'visible' => true,
					    'reset_params' => true
					),
					array(
					    'label' => 'Logistics',
					    'corename' => 'feedback.logistics',
					    'title' => 'logistics',
					    'module' => 'core',
					    'controller' => 'feedback',
					    'action' => 'logistics',
					    'route' => 'main-module',
					    'visible' => true,
					    'reset_params' => true
					),
					array(
					    'label' => 'Programme',
					    'corename' => 'feedback.programme',
					    'title' => 'programme',
					    'module' => 'core',
					    'controller' => 'feedback',
					    'action' => 'programme',
					    'route' => 'main-module',
					    'visible' => true,
					    'reset_params' => true
					),
					array(
					    'label' => 'Presentations',
					    'title' => 'Rate presentations',
					    'module' => 'core',
					    'controller' => 'schedule',
					    'action' => 'list',
					    'route' => 'schedule',
					    'params' => array('f'=>1),
					    'visible' => true,
					    'reset_params' => true
					)
				)
			)
		));

		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('ratepres', 'json')
					->addActionContext('ratings', 'json')
					->initContext();
		
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}

	public function indexAction()
	{
		// this acts as auth
		$this->_feedbackModel->getFeedbackId();

		// redirect to schedule list action
		$this->_helper->flashMessenger('Thank you for letting us know how to get even better!');
		$this->_helper->_redirector->gotoUrl('/core/feedback/participant/');
	}

	/**
	 * Handles new/edit actions for multiple feedback sections
	 * The 'feedbacksection' route takes care of routing defined section requests here
	 *
	 */
	public function feedbacksectionAction()
	{
		// this acts as auth
		$id = $this->_feedbackModel->getFeedbackId();
		if (!$id) {
			$this->_helper->flashMessenger('Feedback is closed.');
			return $this->_helper->redirector->gotoRoute(array('controller'=>'schedule', 'action'=>'list'), 'grid');		
		}

		// set section and form names from parameter
		$request = $this->getRequest();
		$section = $request->getParam('section');
		$formName = 'feedback'.ucfirst($section);

		// set active nav for pages (this is not automatic because the use of a catchall action)
		if ($page = $this->view->navigation()->menu()->findOneBy('corename', 'feedback.'.$section) ) {
			$page->setActive();
		}

		// No post; display form
		if ( !$request->isPost() )  {
			$this->view->feedbackForm = $this->_feedbackModel->getForm($formName);
			// populate form with defaults
			$defaults = ($row = $this->_feedbackModel->getFeedbackById($id, $section) )
				? $row->toArray()
				: array('id' => $id);
			$this->view->feedbackForm->setDefaults($defaults);

			return $this->render($section);
		}

		// try to persist feedback
		if ( $this->_feedbackModel->saveFeedback($request->getPost(), $section) === false ) {
			$this->view->feedbackForm = $this->_feedbackModel->getForm($formName);
			return $this->render($section);
		}

		// everything went OK, redirect
		$this->_helper->flashMessenger('Succesfully saved feedback, please continue with the other sections linked above');
		return $this->_helper->redirector->gotoRoute(array('controller'=>'feedback', 'action'=>'index'));
	}

	/**
	 * Get all presentation ratings
	 *
	 */
	public function ratingsAction()
	{
		// this doubles as auth
		$id = $this->_feedbackModel->getFeedbackId();
		$this->view->defaults = $this->_feedbackModel->getPresentationRatings($id);
	}

	/**
	 * Update feedback ratings for presentations
	 *
	 */
	public function ratepresAction()
	{
		// this doubles as auth
		$id = $this->_feedbackModel->getFeedbackId();

		$feedbackData = $this->getRequest()->getParam('feedback');
		$feedbackData = explode('_', $feedbackData);

		$this->view->rating = $this->_feedbackModel->ratePresentation(
			$id, $feedbackData
		);
	}

	/**
	 * Send out feedback codes to all conference participants
	 *
	 */
	public function mailallAction()
	{
		$request = $this->getRequest();

		if (false == $participants = $this->_feedbackModel->getParticipants() ) {
			return;
		}

		$this->view->participants = $participants;
		$this->view->dummy = $request->getParam('dummy');
		if ( !$request->isPost() )  {
			$this->view->mailForm = $this->_feedbackModel->getForm('feedbackMail');
			return $this->render();
		}

		$conference = Zend_Registry::get('conference');

		Zend_Controller_Action_HelperBroker::addHelper(new TA_Controller_Action_Helper_SendEmail());
		$emailHelper = $this->_helper->sendEmail;

		$this->view->sent = array();

		foreach ($participants as $participant) {
			$emailHelper->sendEmail(array(
				'dummy' => $request->getParam('dummy'),
				'template' => 'feedback/codes',
				'html' => true,
				'subject' => $conference['abbreviation'] . ' Feedback',
				'to_email' => $participant['email'],
				'to_name' => $participant['fname'].' '.$participant['lname']
			), $participant);

			$this->view->sent[] = array(
				'email' => $participant['email']
			);
		}

		// save event log
		if ($this->view->dummy == 0) {
			$eventlogModel = new Core_Model_Eventlog();
			$eventlogModel->saveEventlog(array(
			    'event_type' => __METHOD__,
			    'timestamp' => 'now()'
			));
		}
	}

	/**
	 * Send out feebdack code to one email
	 *
	 */
	public function mailtoAction()
	{
		$conference = Zend_Registry::get('conference');

		Zend_Controller_Action_HelperBroker::addHelper(new TA_Controller_Action_Helper_SendEmail());
		$emailHelper = $this->_helper->sendEmail;

		$form = $this->_feedbackModel->getForm('feedbackMailto');

		if ( !$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost()) )  {
			$this->view->mailForm = $form;
			return $this->render();
		}

		$emailHelper->sendEmail(array(
		    'dummy' => false,
		    'template' => 'feedback/codes',
		    'html' => true,
		    'subject' => $conference['abbreviation'] . ' Feedback',
		    'to_email' => $form->getValue('email')
		), array('uuid' => $this->_feedbackModel->createFeedbackCode()) );

		$this->view->email = $form->getValue('email');
	}

}
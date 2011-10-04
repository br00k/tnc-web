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
 * PresentationController
 *
 * @package Core_Controllers
 */ 
class Core_PresentationController extends Zend_Controller_Action implements Zend_Acl_Resource_Interface
{

	private $_presentationModel;

	public function init()
	{
		$this->_presentationModel = new Core_Model_Presentation();
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
		return 'Presentation';
	}

	public function indexAction()
	{
		return $this->_forward('list');
	}

	/**
	 * Show one presentation based on presentation_id
	 *
	 */
	public function showAction()
	{
		$request = $this->getRequest();
		$this->view->Stylesheet('schedule.css');

		$id = (int) ($request->getParam('id')) ? $request->getParam('id') : $request->getParam('presentation_id');

		$this->view->presentation = $this->_presentationModel->getAllPresentationDataById($id);

		return $this->render('show');
	}

	public function listAction()
	{
		$this->view->grid = $this->_presentationModel->getPresentations(
			$this->_getParam('page', 1),
			array($this->_getParam('order', null), $this->_getParam('dir', 'asc'))
		);

		$this->view->grid['params']['order'] = $this->_getParam('order');
		$this->view->grid['params']['dir'] = $this->_getParam('dir');
		$this->view->grid['params']['controller'] = $this->getRequest()->getControllerName();
		// assign model to view variable, then from the view I can query the ACL
		$this->view->model = $this->_presentationModel;
	}

	private function displayForm()
	{
		$this->view->presentationForm = $this->_presentationModel->getForm('presentation');
		return $this->render('formNew');
	}

	public function editAction()
	{
		$request = $this->getRequest();
		$this->view->id = (int) $request->getParam('id');

		// No post; display form
		if ( !$request->isPost() )  {
			$this->view->presentationForm = $this->_presentationModel->getForm('presentationEdit');
			// populate form with defaults
			$this->view->presentationForm->setDefaults(
				$this->_presentationModel->getPresentationById($this->_getParam('id'))->toMagicArray('dd/MM/yyyy')
			);
			return $this->render('formEdit');
		}
		// try to persist item
		if ( $this->_presentationModel->savePresentation($request->getPost(), 'edit') === false ) {
			$this->view->presentationForm = $this->_presentationModel->getForm('presentationEdit');
			return $this->render('formEdit');
		}

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Succesfully edited record');
		return $this->_helper->lastRequest();
	}

	public function deleteAction()
	{
		if ( false === $this->_presentationModel->delete($this->_getParam('id')) ) {
			throw new TA_Model_Exception('Something went wrong with deleting the user');
		}
		return $this->_helper->redirector->gotoRoute(array('controller'=>'presentation', 'action'=>'list'), 'grid');
	}

	/**
	 * Delete user link
	 *
	 */
	public function deleteuserlinkAction()
	{
		$this->_presentationModel->deleteSpeaker($this->_getParam('id'));
		return $this->_helper->lastRequest();
	}

	public function newAction()
	{
		$request = $this->getRequest();

		// No post; display form
		if ( !$request->isPost() )  {
			return $this->displayForm();
		}

		// try to persist
		if ( ($id = $this->_presentationModel->savePresentation($request->getPost())) === false ) {
			return $this->displayForm();
		}

		// everything went OK, redirect to list action
		$this->_helper->flashMessenger('Succesfully created the presentation. You can now proceed with adding more information');

		return $this->_helper->redirector->gotoRoute(array(
			'controller'=>'presentation', 'action'=>'speakers', 'id'=> $id
		), 'main-module');
	}

	/**
	 * Show/save users linked to this presentation
	 *
	 */
	public function speakersAction()
	{
		$request = $this->getRequest();
		// @todo: i can probably make this shorter - see userController
		$id = (int) ($request->getParam('id')) ? $request->getParam('id') : $request->getParam('presentation_id');
		// required by subnavigation
		$this->view->id = (int) $request->getParam('id');

		// No post; display form
		if ( !$request->isPost() )  {
			$form = $this->view->presentationUserForm = $this->_presentationModel->getForm('presentationUser');
			$form->setDefaults(array(
			   	'presentation_id' => $id
			));
			$form->getElement('user_id')->setTaRow(
				$this->_presentationModel->getPresentationById($id)
			);
			return $this->render('speakers');
		}

		// persist user/presentation mapping
		if ( $this->_presentationModel->saveSpeakers($request->getPost()) === false ) {
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
		// required by subnavigation
		$this->view->id = (int) $request->getParam('id');

		// No post; display form
		if ( !$request->isPost() )  {
			return $this->_saveFiles();
		}

		// try to persist presentation/files
		if ( $this->_presentationModel->saveFiles($request->getPost()) === false ) {
			return $this->_saveFiles();
		}

		// everything went OK
		return $this->_helper->lastRequest();
	}

	/**
	 * Helper method for filesAction
	 *
	 */
	private function _saveFiles()
	{
		$request = $this->getRequest();
		$id = (int) ($request->getParam('id')) ? $request->getParam('id') : $request->getParam('presentation_id');

		$form = $this->view->presentationFilesForm = $this->_presentationModel->getForm('presentationFiles');
		// workaround to fix permission problem
		$form->setAction('/core/presentation/files/id/'.$id);
		$form->setDefaults(array(
		   	'presentation_id' => $id
		));

		// set linked files to magic file elements
		foreach ($files = $this->_presentationModel->getFiles($id) as $file) {
		   	$form->files->{$file->core_filetype}->setTaFile(
		    	$file
		   	);
		}
		return $this->render('files');
	}

	/**
	 * Import submissions to presentations
	 * The title of a submission is used as the title of a presentation
	 *
	 */
	public function importAction()
	{
		$request = $this->getRequest();
		$submissionModel = new Core_Model_Submit();
		$conference = Zend_Registry::get('conference');

		// No post; display form
		if ( !$request->isPost() ) {
			$this->view->importForm =
			$this->_presentationModel->getForm('submitImport')->setDefaults(
				array(
		   			'status' => 1,
		   			'submit_start' => $conference['submit_start']->get('dd/MM/yyyy'),
		   			'submit_end' => $conference['submit_end']->get('dd/MM/yyyy')
		   		)
			);
			return $this->render('formImport');
		}

		if ($import = $this->_presentationModel->linkSubmissions(
				$submissionModel->getAcceptedSubmissions($request->getPost()), $request->getPost()
			)) {
				$this->_helper->flashMessenger('Succesfully imported '.count($import).' submissions');
				$eventlogModel = new Core_Model_Eventlog();
				$eventlogModel->saveEventlog(array(
				    'event_type' => __METHOD__,
				    'timestamp' => 'now()'
				));
			} else {
				$this->_helper->flashMessenger('No submissions to import');
			}

		// everything went OK, redirect
		return $this->_helper->redirector->gotoRoute(array('controller'=>'presentation', 'action'=>'list'), 'grid');
	}

}










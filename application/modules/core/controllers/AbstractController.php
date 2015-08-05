<?php
abstract class Core_AbstractController extends Zend_Controller_Action
{
	public function init()
	{
		$this->_conference = Zend_Registry::get('conference');
    	$this->view->messages = $this->_helper->flashMessenger->getMessages();
    	$page = $this->view->navigation()->findOneBy('controller', $this->getRequest()->getControllerName() );
		$subPage = $page->findOneBy('action', $this->getRequest()->getActionName());

		// add controller specific css
		if ($page->css || $subPage->css) {
    		$this->view->headLink()->appendStylesheet(
    			'/includes/'.strtolower($this->_conference['abbreviation']).'/css/' . $this->getRequest()->getControllerName() . '.css'
    		);
		}

		// three column layout?
		if ($subPage && $subPage->threeColumnLayout) {
			$this->view->threeColumnLayout = true;
		}
	}


}
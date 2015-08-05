<?php

class Core_IndexController extends Zend_Controller_Action
{

	public function init()
	{
    	$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}

    public function indexAction()
    {
    	$this->view->threeColumnLayout = true;
		$this->view->headScript()->appendFile('/js/jwplayer.js');
	}
	
}


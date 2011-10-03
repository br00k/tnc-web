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
 * @revision   $Id: AbstractController.php 618 2011-09-29 11:18:54Z visser $
 */
abstract class Webdemo_AbstractController extends Zend_Controller_Action
{
	protected $_sharedViews = false;
	
	protected $_conference = array();

	/**
	 * All controllers will use the views that are stored in
	 * modules/rest/views/scripts/_shared/
	 *
	 * This method is useful if the same actions in
	 * different controllers share the same views
	 *
	 * This method can be used to control the shared views
	 * from the controller
	 *
	 * @param boolean $flag
	 */
	protected function enableSharedViews($flag=true)
	{
		if($flag) {
			$this->_helper->viewRenderer->setViewScriptPathSpec('_shared/:action.:suffix');
		}
		else {
			$this->_helper->viewRenderer->setViewScriptPathSpec(':action.:suffix');
		}
	}

	public function init()
	{
		$this->_conference = Zend_Registry::get('conference');
    	$this->view->messages = $this->_helper->flashMessenger->getMessages();
    	$page = $this->view->navigation()->findBy('controller', $this->getRequest()->getControllerName() );

		// add controller specific css
		if ($page->css) {
    		$this->view->headLink()->appendStylesheet(
    			'/includes/'.strtolower($this->_conference['abbreviation']).'/css/' . $this->getRequest()->getControllerName() . '.css'
    		);
		}
		
		// three column layout?
		$subPage = $page->findOneBy('action', $this->getRequest()->getActionName());
		if ($subPage && $subPage->threeColumnLayout) {
			$this->view->threeColumnLayout = true;
		}
		
		// set globally shared views
		if ($this->_sharedViews) {
			$this->enableSharedViews(true);
		}
	}
	
	/**
	 * Catch all method
	 *
	 * With this you can simply use /webdemo/<controller>/<action> without 
	 * having to define the action methods in the controller.
	 *
	 */
	public function __call($method, $args)
	{
	    if ('Action' == substr($method, -6)) {
	        $action = $this->getRequest()->getActionName();
	        return $this->render($action);
	    }

	    throw new Exception('Invalid method');
	}
}
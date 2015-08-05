<?php
abstract class Rest_AbstractController extends Zend_Rest_Controller 
{
	protected $_sharedViews = false;
	
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
		$this->_helper->contextSwitch()
			 ->addActionContext('get', array('xml','json'))
			 ->addActionContext('post',  array('xml','json'))
			 ->initContext();
		$this->_helper->layout()->disableLayout();
		
		// can be used to set the shared views globally
		if ($this->_sharedViews) {
			$this->enableSharedViews(true);
		}
	}
	
	public function indexAction()
    {
    	$this->_forward('get');
    }
}
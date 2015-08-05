<?php
/**
 * Override the standard Url View helper
 * 
 * If the controller is not set in the $urlOptions then it is taken 
 * from the request object and added to the $urlOptions array
 *
 * This is very useful for my _grid.phtml partial view 
 * 
 */
class Core_View_Helper_Url extends Zend_View_Helper_Url
{
    public function url(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
    	
    	if (!isset($urlOptions['controller'])) { 
    		$urlOptions['controller'] = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
    	}
		
        return parent::url($urlOptions, $name, $reset, $encode);
    }
}

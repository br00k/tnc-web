<?php
/**
 * Performs header detection and sets a value 'format' to json or xml
 *
 * @todo conflicts with ActionContext switch
 */
class Application_Plugin_Accept extends Zend_Controller_Plugin_Abstract
{

	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
    	$header = $request->getHeader('Accept');
    	if (strstr($header,'application/json')) {
    		$request->setParam('format','json');
    	} elseif (strstr($header,'application/xml')) {
    		$request->setParam('format','xml');
    	} else {
    		$request->setParam('format','html');
    	}
    }
    
}
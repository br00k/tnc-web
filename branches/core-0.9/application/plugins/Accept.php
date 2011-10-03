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
 * @revision   $Id: Accept.php 619 2011-09-29 11:20:22Z gijtenbeek $
 */

/**
 * Performs header detection and sets a value 'format' to json or xml
 *
 * @todo Fix conflict with ActionContext switch
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
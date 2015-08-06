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
 * @revision   $Id: Diagnostic.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */

/**
 * Records status of the Response Object through all application phases
 *
 * @package Application_Plugin 
 */
class Application_Plugin_Diagnostic extends Zend_Controller_Plugin_Abstract
{

	// File handle
	private $_fh;
	private $_hooks;

	/**
	 *
	 * @param string $logfile = filename (see Bootstrap.php)
	 * @param string $mode = 'a' = appends; 'w' = overwrites
	 * @param array $hooks = array of 1 or 0 in this order:
	 * 		array(	0 => routestartup,
	 * 				1 => routeshutdown,
	 * 				2 => dispatchloopstartup,
	 * 				3 => dispatchloopshutdown,
	 * 				4 => predispatch,
	 * 				5 => postdispatch
	 */
	public function __construct($logfile, $mode, $hooks)
	{
		if (isset($mode)) {
			$mode = (strtolower($mode) == 'a') ? 'a' : 'w';
		}
		$this->_fh = fopen($logfile, $mode);
		$this->_hooks = $hooks;
	}
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
		if ($this->_hooks[0]) {
			$message = 'ROUTE STARTUP:'.' '.date('Y-m-d H:i:s', time()).PHP_EOL.
						var_export($this->getResponse(), TRUE).PHP_EOL;
			fputs($this->_fh, $message);
		}
	}
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
		if ($this->_hooks[1]) {
			$message = 'ROUTE SHUTDOWN:'.' '.date('Y-m-d H:i:s', time()).PHP_EOL.
						var_export($this->getResponse(), TRUE).PHP_EOL;
			fputs($this->_fh, $message);
		}
	}
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{
		if ($this->_hooks[2]) {
			$message = 'LOOP STARTUP:'.' '.date('Y-m-d H:i:s', time()).PHP_EOL.
						var_export($this->getResponse(), TRUE).PHP_EOL;
			fputs($this->_fh, $message);
		}
	}
	public function dispatchLoopShutdown()
	{
		if ($this->_hooks[3]) {
			$message = 'LOOP SHUTDOWN:'.' '.date('Y-m-d H:i:s', time()).PHP_EOL.
						var_export($this->getResponse(), TRUE).PHP_EOL;
			fputs($this->_fh, $message);
		}
	}
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		if ($this->_hooks[4]) {
			$message = 'PRE DISPATCH:'.' '.date('Y-m-d H:i:s', time()).PHP_EOL.
						var_export($this->getResponse(), TRUE).PHP_EOL;
			fputs($this->_fh, $message);

			$router = Zend_Controller_Front::getInstance()->getRouter();

			$output =
			"Used Route: <b>".$router->getCurrentRouteName()."</b><br>\n".
			"Abbreviation: ".$request->getParam('abbreviation')."<br>\n".
			"Language: ".$request->getParam('language')."<br>\n".
			"Module: ".$request->getModuleName()."<br>\n".
			"Controller: ".$request->getControllerName()."<br>\n".
			"Action: ".$request->getActionName()."<br>\n".
			"----------<br>\n";
		   	foreach ($request->getParams() as $param => $value) {
				$output .= '&nbsp;&nbsp;&nbsp;'.$param.':'.$value."<br>\n";
			}
			echo $output;
		}
	}
	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{
		if ($this->_hooks[5]) {
			$message = 'POST DISPATCH:'.' '.date('Y-m-d H:i:s', time()).PHP_EOL.
						var_export($this->getResponse(), TRUE).PHP_EOL;
			fputs($this->_fh, $message);
		}
	}

}
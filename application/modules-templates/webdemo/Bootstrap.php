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
 * @revision   $Id: Bootstrap.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */

class Webdemo_Bootstrap extends Zend_Application_Module_Bootstrap
{
	public function _initRoutes()
	{
        $this->bootstrap('frontController');
        $front = $this->frontController;
        $router = $front->getRouter();

		$route = new Zend_Controller_Router_Route(
			'/webdemo/:controller/:action/*',
			array (
				'module' => 'webdemo',
				'controller' => 'index',
				'action' => 'index'
			)
		);

		$router->addRoute('webdemo', $route);

		$route = new Zend_Controller_Router_Route(
			'/webdemo/sponsors',
			array (
				'module' => 'webdemo',
				'controller' => 'index',
				'action' => 'sponsors'
			)
		);

		$router->addRoute('sponsors', $route);

		$route = new Zend_Controller_Router_Route(
			'/webdemo/contacts',
			array (
				'module' => 'webdemo',
				'controller' => 'index',
				'action' => 'contacts'
			)
		);

		$router->addRoute('contacts', $route);

		$route = new Zend_Controller_Router_Route(
			'/webdemo/camera',
			array (
				'module' => 'webdemo',
				'controller' => 'index',
				'action' => 'camera_instructions'
			)
		);

		$router->addRoute('camerainstructions', $route);

		$route = new Zend_Controller_Router_Route(
			'/coverage',
			array (
				'module' => 'webdemo',
				'controller' => 'media',
				'action' => 'coverage'
			)
		);

		$router->addRoute('coverage', $route);

      	$route = new Zend_Controller_Router_Route_Regex(
        	'webdemo/media/(archive|stream)/(\d+[A-Z]{1})',
			array(
				'module'	=> 'webdemo',
				'controller'=> 'media'
			),
			array(
				1 => 'action',
				2 => 'stream'
			),
			'web/media/%s/%s'
    	);
    	$router->addRoute('stream', $route);

	}

}
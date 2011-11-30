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
 * @revision   $Id: Bootstrap.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */
 
/**
 * REST specific bootstrapper
 *
 * @package Rest
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Rest_Bootstrap extends Zend_Application_Module_Bootstrap
{
/**
     * Initialize all module-specific plugins
     */
    protected function _initPlugins()
    {
    	// get the frontcontroller so we can register plugins with it
    	$front = $this->getApplication()
    	              ->bootstrap('frontcontroller')
    	              ->getResource('frontcontroller');

    	// register the plugins (here we have only one)
        # $front->registerPlugin(new Terena_Plugin_Something());
    }

	protected function _initRoutes()
    {
        $this->bootstrap('frontController');
        $front = $this->frontController;
        $router = $front->getRouter();

        // Specifying the "rest" module for RESTful services:
		$restRoute = new Zend_Rest_Route($front, array(), array('rest'));
		$router->addRoute('rest', $restRoute);
    }
}
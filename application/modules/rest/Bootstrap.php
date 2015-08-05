<?php

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
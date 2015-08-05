<?php
/**
 * Zend loads ALL Bootstrap's instead of the one needed for the module, so
 * to load module specific config I have to work around it with this plugin
 */
class Application_Plugin_Module extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
		if ($request->getModuleName() == 'web') {

			$config = new Zend_Config_Ini(
				APPLICATION_PATH.'/configs/web.ini',
				'development'
			);

			Zend_Registry::set('webConfig', $config);
		}

	}


}
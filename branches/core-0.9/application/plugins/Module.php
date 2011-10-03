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
 * @revision   $Id: Module.php 619 2011-09-29 11:20:22Z gijtenbeek $
 */

/**
 * Zend loads ALL Bootstrap's instead of the one needed for the module.
 * This plugin makes it possible to load module specific config
 */
class Application_Plugin_Module extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
		if ($request->getModuleName() == 'web') {

			$conference = Zend_Registry::get('conference');

			$config = new Zend_Config_Ini(
				APPLICATION_PATH.'/configs/web.ini',
				'development'
			);

			Zend_Registry::set('webConfig', $config);
		}

	}

}
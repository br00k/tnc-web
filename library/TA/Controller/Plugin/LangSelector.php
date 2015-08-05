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
 * @revision   $Id: LangSelector.php 29 2011-10-05 20:36:08Z gijtenbeek@terena.org $
 */
 
/**
 * Sets language
 *
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 * @package TA_Controller
 * @subpackage Plugin
 */
class TA_Controller_Plugin_LangSelector extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
        // Get our translate object from registry.
        $translate = Zend_Registry::get('Zend_Translate');
        $currLocale = $translate->getLocale();
        // Create Session block and save the locale
        $session = new Zend_Session_Namespace('session');

        $lang = $request->getParam('language','');
        // Register all your "approved" locales below.
        switch ($lang) {
            case "nl":
                $langLocale = 'en_GB';
                break;
            case "en":
                $langLocale = 'en_US';
                break;
            default:
                /**
                 * Get a previously set locale from session or set
                 * the current application wide locale (set in
                 * Bootstrap) if not.
                 */
				$langLocale = isset($session->lang) ? $session->lang : $currLocale;
        }

        $newLocale = new Zend_Locale();
        $newLocale->setLocale($langLocale);
        Zend_Registry::set('Zend_Locale', $newLocale);        

        $translate->setLocale($langLocale);
        $session->lang = $langLocale;

        // Save the modified translate back to registry
        Zend_Registry::set('Zend_Translate', $translate);
	}

	/**
	 * @todo: This has nothing to do with the langselector, so maybe move it to its own plugin
	 *
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{
    	// Don't save ajax call url's as the last request
   		if (!$request->isXmlHttpRequest()) {
			$lastRequest = Zend_Controller_Action_HelperBroker::getStaticHelper('lastRequest');
			$lastRequest->saveRequestUri($request->getRequestUri());
		}
	}

}
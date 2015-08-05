<?php
/**
 *
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
                $langLocale = 'nl_NL';
                break;
            case "en":
                $langLocale = 'en_US';
                break;
            case "hu":
            	$langLocale = 'hu_HU';
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
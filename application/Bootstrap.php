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
 * @revision   $Id: Bootstrap.php 79 2012-12-05 09:44:49Z gijtenbeek@terena.org $
 */

/**
 * Bootstrapper
 *
 * @package Core
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	public $frontController;

	/**
	 * Initialize mailer
	 *
	 */
	protected function _initDefaultEmailTransport()
	{
		$this->bootstrap('mail');
		$transport = $this->getResource('mail');
	}

	/**
	 * Initialize config object and store in global Registry
	 *
	 */
    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions());

 		$frontendOptions = array(
			'automatic_serialization' => true,
		    'master_files' => array(
		    	APPLICATION_PATH.'/configs/application.ini'
		    )
		);

		$backendOptions = array(
		    'cache_dir' => APPLICATION_PATH.'/../cache'
		);

		$cache = Zend_Cache::factory('File',
		                             'File',
		                             $frontendOptions,
		                             $backendOptions);
		#$cache->save($config, 'config');

        Zend_Registry::set('config', $config);
        return $config;
    }

    /**
     * Initialize Logging
     *
     */
	protected function _initLogging()
	{
		$this->bootstrap('log');
		Zend_Registry::set('log', $log = $this->getResource('log'));

		$mail = new Zend_Mail();
		$config = Zend_Registry::get('config');
		$mail->addTo($config->core->debugMailTo);

		$layout = new Zend_Layout();
		$layout->setLayout('errormail');

		$writer = new Zend_Log_Writer_Mail($mail, $layout);

		$writer->setSubjectPrependText('CORE Error');
		$writer->addFilter(Zend_Log::CRIT);
		#$log->addWriter($writer);
	}

	/**
	 * Initialize Locale
	 *
	 */
	protected function _initLocale()
	{
		$this->bootstrap('log');
		$log = $this->getResource('log');

        // Set application wide source Locale
        $locale = new Zend_Locale('en_GB');

	   	// Set up and load the translations
        $translate = new Zend_Translate(
        	array(
        		'adapter' => 'gettext',
        		'content' => APPLICATION_PATH . '/../languages/',
            	'scan' => Zend_Translate::LOCALE_FILENAME,
            	'log' => $this->getResource('log'),
            	'disableNotices' => false, // Get rid of annoying 'not translated' notice
            	'logUntranslated' => false, // Set to true if you debug
            )
        );

	   	// Add translation to all i18n compatible components
        Zend_Registry::set('Zend_Locale', $locale);
        Zend_Registry::set('Zend_Translate', $translate);

        return $translate;
	}

	/**
	 * Initialize view
	 *
	 */
	protected function _initViewSettings()
	{
		$this->bootstrap('view');
		$this->_view = $this->getResource('view');
		$this->_view->doctype('XHTML1_STRICT');
	}

	/**
	 * Initialize Database cache
	 *
	 * @todo move to config?
	 */
	protected function _initDbCache()
	{
		$this->bootstrap('cachemanager');
		$this->_cache = $cache = $this->getResource('cachemanager')
		   						  ->getCache('simple');
		if ($this->getEnvironment() === 'production') {
			Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
		}
	}

	/**
	 * Initialize autoloader for Models/Forms/Plugins
	 *
	 */
	protected function _initModuleResourceAutoloader()
	{
		$this->_resourceLoader = new Zend_Application_Module_Autoloader(
			array(
				'basePath' => APPLICATION_PATH.'/modules/core',
				'namespace' => 'Core',
				'resourceTypes' => array(
					'modelResources'=> array(
						'path' => '/models/resources',
						'namespace' => 'Resource' // component namespace, to append to the base namespace to construct classname
					)
				)

			));
	}

	/**
	 * Initialize custom CORE Routing
	 *
	 */
	protected function _initRoutes()
	{
		$this->bootstrap('frontController');
		$router = $this->frontController->getRouter();
		$router->removeDefaultRoutes();

		$userCountry = (Zend_Auth::getInstance()->getIdentity())
			? strtolower(Zend_Auth::getInstance()->getIdentity()->country)
			: 'en';

		$mainRoute = new Zend_Controller_Router_Route_Hostname(
		   getenv('HTTP_HOST'),
		   array (
		   	'language' => ($userCountry) ? $userCountry : 'en'
		   ),
		   array (
		   	'abbreviation' => '\w+'
		   )
		);

      	$defaultRoute = new Zend_Controller_Router_Route_Static('',
          	array(
          		'module'	 => 'core',
          		'controller' => 'index',
          		'action' => 'index'
          	)
      	);
      	$router->addRoute('main-default', $mainRoute->chain($defaultRoute));

		$moduleRoute = new Zend_Controller_Router_Route(
			':module/:controller/:action/*',
			array (
				'module'  => 'core',
				'controller'=>'index',
				'action'	=> 'index'
			)
		);

		$router->addRoute('main-module', $mainRoute->chain($moduleRoute));

		$languageRoute = new Zend_Controller_Router_Route(
			':language/:module/:controller/:action/*',
			array (
				'module'  => 'core',
				'controller'=>'index',
				'action'	=> 'index',
			),
			array (
				'language' => '[a-z]{2}'
			)
		);
		$router->addRoute('main-language', $mainRoute->chain($languageRoute));

		// @todo: removed language, so that no longer works!
		$gridRoute = new Zend_Controller_Router_Route(
			':module/:controller/list/:order/:dir/:page',
			array (
				'module' => 'core',
				'controller' => 'index',
				'order' => '',
				'dir' => 'asc',
				'page' => 1,
				'action' => 'list'
			),
			array (
				'page' => '\d+'
			)
		);
		$router->addRoute('grid', $mainRoute->chain($gridRoute));

		$scheduleRoute = new Zend_Controller_Router_Route(
			'/core/schedule/list/*',
			array (
				'module' => 'core',
				'controller' => 'schedule',
				'action' => 'list'
			)
		);
		$router->addRoute('schedule', $mainRoute->chain($scheduleRoute));

		// @todo: this doesn't allow for languages!
		// added optional format parameter: (?:/(\d+))?
		// link can now be: /core/submit/mail/1/json
    	$gridActionsRoute = new Zend_Controller_Router_Route_Regex(
    		'(\S+)/(\S+)/(edit|delete|reviewers|deletereviewer|chairs|deletechair|deletepresentation|mail|subscribe|unsubscribe)/(\d+)(?:/(\S+))?',
			array(
				'language'		=> 'en',
				'module'        => 'core',
				'controller'    => '',
				'action'		=> 'edit',
				'format'		=> ''
			),
			array(1 => 'module', 2 => 'controller', 3 => 'action', 4 => 'id', 5 => 'format'),
			'%s/%s/%s/%d/%s'
    	);
    	$router->addRoute('gridactions', $mainRoute->chain($gridActionsRoute));

      	$route = new Zend_Controller_Router_Route_Regex(
        	'core/review/list/(\d+)',
			array(
				'lang'		=> ':lang',
				'module'	=> 'core',
				'controller'=> 'review',
				'action'	=> 'list'
			),
			array(
				1 => 'id'
			),
			'core/review/list/%d'
    	);
    	$router->addRoute('reviewlist', $mainRoute->chain($route));

      	$route = new Zend_Controller_Router_Route_Regex(
        	'core/review/listpersonal/(\d+)',
			array(
				'lang'		=> ':lang',
				'module'	=> 'core',
				'controller'=> 'review',
				'action'	=> 'listpersonal'
			),
			array(
				1 => 'id'
			),
			'core/review/listpersonal/%d'
    	);
    	$router->addRoute('reviewlistpersonal', $mainRoute->chain($route));

	   	$route = new Zend_Controller_Router_Route(
        	'/core/review/new/:id',
			array(
				'lang'		=> ':lang',
				'module'	=> 'core',
				'controller'=> 'review',
				'action'	=> 'new',
				'id'		=> ':id'
			)
    	);
    	$router->addRoute('reviewnew', $mainRoute->chain($route));

    	$route = new Zend_Controller_Router_Route(
        	'/core/conference/timeslots/:id',
			array(
				'lang'		=> ':lang',
				'module'	=> 'core',
				'controller'=> 'conference',
				'action'	=> 'timeslots',
				'id'		=> ':id'
			),
			array(
				'id' => '\d+'
			)
    	);
    	$router->addRoute('timeslots', $mainRoute->chain($route));

     	$route = new Zend_Controller_Router_Route(
        	'/getfile/:id',
			array(
				'module'	=> 'core',
				'controller'=> 'file',
				'action'	=> 'getfile'
			),
			array(
				'id' => '\d+'
			)
    	);
    	$router->addRoute('getfile', $mainRoute->chain($route));

      	$route = new Zend_Controller_Router_Route_Regex(
        	'core/(file|session|presentation|user|event|poster)/(\d+)',
			array(
				'module'	=> 'core',
				'action'	=> 'show'
			),
			array(
				1 => 'controller',
				2 => 'id'
			),
			'core/%s/%d'
    	);
    	$router->addRoute('oneitem', $mainRoute->chain($route));

     	$route = new Zend_Controller_Router_Route(
        	'core/feedback/:uuid',
			array(
				'module'	=> 'core',
				'controller'=> 'feedback',
				'action'	=> 'index'
			),
			array(
				'uuid' => '[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89AB][0-9a-f]{3}-[0-9a-f]{12}'
			)
    	);
    	$router->addRoute('feedback', $mainRoute->chain($route));

		// route all feedback section requests to to same action controller
      	$route = new Zend_Controller_Router_Route_Regex(
        	'core/feedback/(general|participant|logistics|programme)',
			array(
				'module'	=> 'core',
				'controller'=> 'feedback',
				'action'	=> 'feedbacksection'
			),
			array(
				1 => 'section'
			),
			'core/feedback/%s'
    	);
    	$router->addRoute('feedbacksection', $mainRoute->chain($route));

		// feedback download results
      	$route = new Zend_Controller_Router_Route_Regex(
        	'core/feedback/getresults/(general|participant|logistics|programme)',
			array(
				'module'	=> 'core',
				'controller'=> 'feedback',
				'action'	=> 'getresults'
			),
			array(
				1 => 'section'
			),
			'core/feedback/getresults/%s'
    	);
    	$router->addRoute('feedbackgetresults', $mainRoute->chain($route));
    	
  		$posterVoteRoute = new Zend_Controller_Router_Route(
			'/postervote',
			array (
				'module' => 'core',
				'controller' => 'poster',
				'action' => 'liststudent'
			)
		);
		$router->addRoute('postervote', $mainRoute->chain($posterVoteRoute));  	

	}

	/**
	 * Initialize Action helpers
	 *
	 */
	protected function _initHelpers()
	{
		Zend_Controller_Action_HelperBroker::addHelper(new TA_Controller_Action_Helper_LastRequest());
	}

	/**
	 * Initialize Resource Plugins
	 *
	 */
	protected function _initPlugins()
	{
		$config = Zend_Registry::get('config');
		if ($config->diagnostic->log) {
			$front = $this->bootstrap('frontcontroller')
    		              ->getResource('frontcontroller');

        	$front->registerPlugin(new Application_Plugin_Diagnostic(
        		$config->diagnostic->log,
        		$config->diagnostic->mode,
        		$config->diagnostic->switch->toArray()
        	));
        }
	}


	/**
	 * Initialize Navigation
	 *
	 * @note If any custom routes are used (see _initRoutes) then you have to list the name of those routes in the corresponding page entry
	 * and set the other route keys to 'default'. If you do not do this, all the links of the navigation items will break!
	 * @note If I set the custom route to 'grid' the sorting is inherited.
	 *
	 * @see TA_Controller_Plugin_NavigationSelector
	 *
	 */
	protected function _initNavigation()
	{
    	$this->bootstrap('frontController');
    	$frontController = $this->getResource('frontController');

		$navigationPlugin = $frontController->getPlugin('TA_Controller_Plugin_NavigationSelector');

		// store the navigation in the resource registry
		return $navigationPlugin->getNavigation();
	}
}
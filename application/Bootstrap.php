<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	public $frontController;

	protected function _initDefaultEmailTransport()
	{
		$this->bootstrap('mail');
		$transport = $this->getResource('mail');
	}

	protected function _initLogging()
	{
		$this->bootstrap('log');
		Zend_Registry::set('log', $log = $this->getResource('log'));

		$mail = new Zend_Mail();
		$mail->addTo('gijtenbeek@terena.org');

		$layout = new Zend_Layout();
		$layout->setLayout('errormail');

		$writer = new Zend_Log_Writer_Mail($mail, $layout);

		$writer->setSubjectPrependText('CORE Error');
		$writer->addFilter(Zend_Log::CRIT);
		#$log->addWriter($writer);
	}

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
	 * Create the Zend_Config object and store to the registry.
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

	protected function _initViewSettings()
	{
		$this->bootstrap('view');
		$this->_view = $this->getResource('view');
		$this->_view->doctype('XHTML1_STRICT');
	}

	protected function _initDbCache()
	{
		$this->bootstrap('cachemanager');
		$this->_cache = $cache = $this->getResource('cachemanager')
		   						  ->getCache('simple');
		if ($this->getEnvironment() === 'production') {
			Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
		}
	}

	/*
	* Autoload Models/Forms/Plugins
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

	protected function _initRoutes()
	{
		$this->bootstrap( 'frontController' );
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

    	$route = new Zend_Controller_Router_Route(
        	'/core/review/list/:id',
			array(
				'lang'		=> ':lang',
				'module'	=> 'core',
				'controller'=> 'review',
				'action'	=> 'list'
			)
    	);
    	$router->addRoute('reviewlist', $mainRoute->chain($route));

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

	}

	/**
	 * Register Action helpers
	 */
	protected function _initHelpers()
	{
		Zend_Controller_Action_HelperBroker::addHelper(new TA_Controller_Action_Helper_LastRequest());
	}

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
	 * @note If any custom routes are used (see _initRoutes) then you have to list the name of those routes in the corresponding page entry
	 * and set the other route keys to 'default'. If you do not do this, all the links of the navigation items will break!
	 * @note If I set the custom route to 'grid' the sorting is inherited...
	 * @todo add proper rendering of submenu items!
	 */
	protected function _initNavigation()
	{
		$this->bootstrap ( 'routes' );
		$router = $this->routes;

        $acl = new Core_Model_Acl_Core();

        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
       		$role = 'guest';
        } else {
        	$role = $auth->getIdentity();
        }

		// store ACL in global registry
        Zend_Registry::set('acl', $acl);

		$pages = array(
		    array(
		    	'label' => 'Schedule',
		    	'title' => 'Schedule',
		    	'module' => 'core',
		    	'controller' => 'schedule',
		    	'action' => 'list',
		    	'resource' => 'Schedule',
		    	'privilege' => 'list',
		    	'class' => 'schedule',
		    	'route' => 'main-module',
		    	'threeColumnLayout' => true,
		    	'reset_params' => true,
		    	'pages' => array(
		    		array(
		    			'label' => 'Speakers',
		    			'title' => 'Speakers',
		    			'module' => 'core',
		    			'controller' => 'user',
		    			'action' => 'speaker',
		    			'resource' => 'User', // ACL baby!!!
		    			'privilege' => 'show',
		    			'route' => 'main-module', // see note!
		    			'reset_params' => true
		    		),
		    		array(
						'label' => 'Schedule',
						'title' => 'Schedule',
						'module' => 'core',
						'controller' => 'schedule',
						'resource' => 'Schedule', // ACL
						'action' => 'list',
						'privilege' => 'list',
						'route' => 'main-module',
						'threeColumnLayout' => true,
						'reset_params' => true
		    		),
		    		array(
		    			'label' => 'Sessions',
		    			'title' => 'Sessions',
		    			'module' => 'core',
		    			'controller' => 'session',
		    			'action' => 'list',
		    			'resource' => 'Session',
		    			'privilege' => 'list',
		    			'route' => 'main-module',
						'threeColumnLayout' => true,
		    			'reset_params' => true
		    		),
		    		array(
		    			'label' => 'Presentations',
		    			'title' => 'Presentations',
		    			'module' => 'core',
		    			'controller' => 'presentation',
		    			'resource' => 'Presentation',
		    			'privilege' => 'list', // ACL, add to show button
		    			'action' => 'list',
		    			'route' => 'main-module',
		    			'reset_params' => true
		    		),
		    		array(
		    			'label' => 'Events',
		    			'title' => 'Events',
		    			'module' => 'core',
		    			'controller' => 'event',
		    			'resource' => 'Event',
		    			'privilege' => 'list',
		    			'action' => 'list',
		    			'threeColumnLayout' => true,
		    			'route' => 'grid',
		    			'reset_params' => true
		    		),
		    		array(
		    			'label' => 'Conferences',
		    			'title' => 'Conference',
		    			'module' => 'core',
		    			'controller' => 'conference',
		    			'action' => 'list',
		    			'resource' => 'Conference',
		    			'route' => 'main-module',
		    			'reset_params' => true
		    		),
		    		array(
		    			'label' => 'Users',
		    			'title' => 'Users',
		    			'module' => 'core',
		    			'controller' => 'user',
		    			'action' => 'list',
		    			'resource' => 'User',
		    			'route' => 'main-module',
		    			'reset_params' => true
		    		),
		    		array(
		    			'label' => 'Locations',
		    			'title' => 'Locations',
		    			'controller' => 'location',
		    			'action' => 'list',
		    			'resource' => 'Location',
		    			'privilege' => 'list',
		    			'route' => 'grid',
		    			'reset_params' => true
		    		),
		    		array(
		    		   'label' => 'Posters',
		    		   'title' => 'Posters',
		    		   'module' => 'core',
		    		   'controller' => 'poster',
		    		   'action' => 'list',
		    		   'resource' => 'Poster',
		    		   'privilege' => 'list',
		    		   'route' => 'grid',
		    		   'reset_params' => true
		    		),
		    		array(
		    		   'label' => 'GN3 Workshops',
		    		   'title' => 'GN3 Workshops',
		    		   'module' => 'web',
		    		   'controller' => 'schedule',
		    		   'action' => 'gn3workshops',
		    		   'threeColumnLayout' => true,
		    		   'route' => 'main-module'
		    		)
		    	)
		    ),
		    array(
		    	'label' => 'Venue',
		    	'title' => 'Venue',
		    	'module' => 'web',
		    	'controller' => 'venue',
		    	'action' => 'index',
		    	'class' => 'venue',
		    	'css' => true,
		    	'route' => 'main-module',
		    	'reset_params' => true,
		    	'pages' => array(
		    		array(
		    			'label' => 'Location',
		    			'title' => 'location',
		    			'module' => 'web',
		    			'controller' => 'venue',
		    			'action' => 'index'
		    		),
		    		array(
		    			'label' => 'Map',
		    			'title' => 'map',
		    			'threeColumnLayout' => true,
		    			'module' => 'web',
		    			'controller' => 'venue',
		    			'action' => 'map'
		    		),
		    		array(
		    			'label' => 'Tourist info',
		    			'title' => 'tourist info',
		    			'module' => 'web',
		    			'controller' => 'venue',
		    			'action' => 'tourist-info'
		    		),
		    		array(
		    			'label' => 'Hotels',
		    			'title' => 'hotels',
		    			'module' => 'web',
		    			'controller' => 'venue',
		    			'action' => 'hotels',
		    			'threeColumnLayout' => true
		    		),
		    		array(
		    			'label' => 'Tours',
		    			'title' => 'tours',
		    			'module' => 'web',
		    			'controller' => 'venue',
		    			'action' => 'tours',
		    			'threeColumnLayout' => true
		    		)
		    	)
		    ),
		    array(
		    	'label' => 'Media',
		    	'title' => 'Media',
		    	'module' => 'web',
		    	'controller' => 'media',
		    	'action' => 'archive',
		    	'class' => 'media',
		    	'route' => 'main-module',
		    	'reset_params' => true,
		    	'pages' => array(
		    		array(
		    			'label' => 'Announcements',
		    			'title' => 'Announcements',
		    			'module' => 'web',
		    			'controller' => 'media',
		    			'action' => 'announcements',
		    			'route' => 'main-module',
		    			'reset_params' => false,
		    			'reset_params' => true,
		    			'threeColumnLayout' => true
		    		),
		    		array(
		    			'label' => 'Live Video',
		    			'title' => 'Live Video',
		    			'module' => 'web',
		    			'visible' => false,
		    			'controller' => 'media',
		    			'action' => 'stream',
		    			'route' => 'main-module',
		    			'reset_params' => false,
		    			'reset_params' => true,
		    			'threeColumnLayout' => true
		    		),
		    		array(
		    			'label' => 'Archived Video',
		    			'title' => 'Archived Video',
		    			'module' => 'web',
		    			'controller' => 'media',
		    			'action' => 'archive',
		    			'route' => 'main-module',
		    			'reset_params' => false,
		    			'reset_params' => true,
		    			'threeColumnLayout' => true
		    		),
		    		array(
		    			'label' => 'Daily impressions',
		    			'title' => 'Daily impressions of TNC',
		    			'module' => 'web',
		    			'controller' => 'media',
		    			'action' => 'video',
		    			'route' => 'main-module',
		    			'reset_params' => false,
		    			'reset_params' => true,
		    			'threeColumnLayout' => true
		    		),
		    		array(
		    			'label' => 'News',
		    			'title' => 'News',
		    			'module' => 'web',
		    			'controller' => 'media',
		    			'action' => 'news',
		    			'route' => 'main-module',
		    			'reset_params' => false,
		    			'reset_params' => true,
		    			'threeColumnLayout' => true
		    		),
		    		array(
		    			'label' => 'Photos',
		    			'title' => 'Photos',
		    			'module' => 'web',
		    			'controller' => 'media',
		    			'action' => 'photos',
		    			'route' => 'main-module',
		    			'reset_params' => false,
		    			'reset_params' => true,
		    			'threeColumnLayout' => true
		    		),
		    		array(
		    			'label' => 'Coverage',
		    			'title' => 'Coverage',
		    			'module' => 'web',
		    			'controller' => 'media',
		    			'action' => 'coverage',
		    			'route' => 'main-module',
		    			'reset_params' => false,
		    			'reset_params' => true,
		    			'threeColumnLayout' => true
		    		)
		    	)
		    ),
		    array(
		    	'label' => 'Participate',
		    	'title' => 'Participate',
		    	'module' => 'web',
		    	'controller' => 'participate',
		    	'action' => 'guidelines',
		    	'class' => 'participate',
		    	'route' => 'main-module',
		    	'css' => true,
		    	'reset_params' => true,
		    	'pages' => array(
		    		array(
		    			'label' => 'Submissions',
		    			'title' => 'List of submitted papers',
		    			'module' => 'core',
		    			'controller' => 'submit',
		    			'resource' => 'Submit', // ACL
		    			'action' => ($acl->isAllowed($role, 'Submit', 'list')) ? 'list' : 'new',
		    			'route' => 'main-module',
		    			'reset_params' => true,
		    			'privilege' => ($acl->isAllowed($role, 'Submit', 'list')) ? 'list' : 'new'
		    		),
		    		array(
		    			'label' => 'Review',
		    			'title' => 'Review',
		    			'module' => 'core',
		    			'controller' => 'review',
		    			'resource' => 'Review', // ACL
		    			'action' => 'list',
		    			'route' => 'main-module',
		    			'reset_params' => false,
		    			'privilege' => 'list',
		    			'reset_params' => true
		    		),
		    		array(
		    		    'label' => 'Guidelines',
		    		    'title' => 'guidelines',
		    		    'module' => 'web',
		    		    'controller' => 'participate',
		    		    'action' => 'guidelines',
		    			'route' => 'main-module'
		    		),
		    		array(
		    		    'label' => 'Topics',
		    		    'title' => 'topics',
		    		    'module' => 'web',
		    		    'controller' => 'participate',
		    		    'action' => 'topics',
		    			'route' => 'main-module',
		    			'threeColumnLayout' => true
		    		),
		    		array(
		    		    'label' => 'Register',
		    		    'title' => 'register',
		    		    'module' => 'web',
		    		    'controller' => 'participate',
		    		    'action' => 'register',
		    			'route' => 'main-module'
		    		),
		    		array(
		    		    'label' => 'Participants',
		    		    'title' => 'participants',
		    		    'module' => 'web',
		    		    'controller' => 'participate',
		    		    'action' => 'participants',
		    			'route' => 'main-module',
		    			'threeColumnLayout' => true
		    		)
		    	)
		    )
		    
		);

		$container = new Zend_Navigation($pages);

		// view helper
		$nav = $this->_view->navigation($container);

        // add ACL and default role to navigation
        $nav->setAcl($acl)->setRole($role);

		// store the navigation in the resource registry
		return $container;
	}
}
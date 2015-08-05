<?php
return array(
	array(
		'label' => 'Schedule',
		'visible' => true,
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
			    'reset_params' => true,
			   	'visible' => true
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
			    'threeColumnLayout' => false,
			    'reset_params' => true,
			    'visible' => true,

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
			   'label' => 'GÉANT Workshops',
			   'title' => 'GN3 Workshops',
			   'visible' => true,
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
	    'visible' => true,
	    'css' => true,
	    'route' => 'main-module',
	    'reset_params' => true,
	    'pages' => array(
	    	array(
	    		'label' => 'Location',
	    		'visible' => true,
	    		'title' => 'location',
	    		'module' => 'web',
	    		'controller' => 'venue',
	    		'action' => 'index'
	    	),
	    	array(
	    		'label' => 'Map',
	    		'title' => 'map',
	    		'threeColumnLayout' => true,
	    		'visible' => true,
	    		'module' => 'web',
	    		'controller' => 'venue',
	    		'action' => 'map'
	    	),
	    	array(
	    		'label' => 'Getting to Porto & around',
	    		'title' => 'Getting to Porto & around',
	    		'threeColumnLayout' => true,
	    		'module' => 'web',
	    		'controller' => 'venue',
	    		'action' => 'gettingto',
	    		'visible' => true
	    	),
	    	array(
	    		'label' => 'Tourist info',
	    		'title' => 'tourist info',
	    		'module' => 'web',
	    		'controller' => 'venue',
	    		'action' => 'tourist-info',
	    		'visible' => true
	    	),
	    	array(
	    		'label' => 'Hotels',
	    		'title' => 'hotels',
	    		'visible' => true,
	    		'module' => 'web',
	    		'controller' => 'venue',
	    		'action' => 'hotels',
	    		'threeColumnLayout' => true
	    	),	    	
	    	array(
	    		'label' => 'Porto City Run',
	    		'title' => 'Porto City Run',
	    		'visible' => true,
	    		'module' => 'web',
	    		'controller' => 'venue',
	    		'action' => 'cityrun',
	    		'threeColumnLayout' => true
	    	),
	    	array(
	    		'label' => 'Tours',
	    		'visible' => false,
	    		'title' => 'tours',
	    		'module' => 'web',
	    		'controller' => 'venue',
	    		'action' => 'tours',
	    		'threeColumnLayout' => true
	    	),
	    	array(
	    		'label' => 'Places to go',
	    		'visible' => false,
	    		'title' => 'Where to eat/drink/shop and visit',
	    		'module' => 'web',
	    		'controller' => 'venue',
	    		'action' => 'wheretogo',
	    		'visible' => false,
	    		'threeColumnLayout' => true
	    	),
	    	array(
	    		'label' => 'Places to drink',
	    		'visible' => false,
	    		'title' => 'Where to get drunk',
	    		'module' => 'web',
	    		'controller' => 'venue',
	    		'action' => 'wheretodrink',
	    		'visible' => false,
	    		'threeColumnLayout' => true
	    	)
	    )
	),
	array(
	    'label' => 'Media',
	    'title' => 'Media',
	    'module' => 'web',
	    'controller' => 'media',
	    'action' => 'news',
	    'class' => 'media',
	    'visible' => true,
	    'css' => true,
	    'route' => 'main-module',
	    'reset_params' => true,
	    'pages' => array(
	    	array(
	    		'label' => 'Live Video',
	    		'title' => 'Live Video',
	    		'module' => 'web',
	    		'visible' => false,
	    		'controller' => 'media',
	    		'action' => 'stream',
	    		'threeColumnLayout' => true
	    	),
	    	array(
	    		'label' => 'Archived Video',
	    		'title' => 'Archived Video',
	    		'module' => 'web',
				'visible' => false,
	    		'controller' => 'media',
	    		'action' => 'archive',
	    		'threeColumnLayout' => true
	    	),
	    	array(
	    		'label' => 'Daily impressions',
	    		'title' => 'Daily impressions of TNC',
	    		'module' => 'web',
				'visible' => false,
	    		'controller' => 'media',
	    		'action' => 'video',
	    		'threeColumnLayout' => true
	    	),
	    	array(
	    		'label' => 'News',
	    		'title' => 'News',
	    		'module' => 'web',
				'visible' => true,
	    		'controller' => 'media',
	    		'action' => 'news',
	    		'threeColumnLayout' => true
	    	),
	    	array(
	    		'label' => 'Photos',
	    		'title' => 'Photos',
	    		'module' => 'web',
	    		'controller' => 'media',
	    		'action' => 'photos',
				'visible' => false,
	    		'threeColumnLayout' => true
	    	),
	    	array(
	    		'label' => 'Coverage',
	    		'title' => 'Coverage',
	    		'module' => 'web',
	    		'controller' => 'media',
	    		'action' => 'coverage',
				'visible' => true,
	    		'threeColumnLayout' => true
	    	),
	    	array(
	    		'label' => 'Promo',
	    		'title' => 'promo',
	    		'module' => 'web',
	    		'controller' => 'media',
	    		'action' => 'promo',
				'visible' => true,
	    		'threeColumnLayout' => true
	    	)
	    )
	),
	array(
	    'label' => 'Participate',
	    'title' => 'Participate',
	    'module' => 'web',
	    'controller' => 'participate',
	    'visible' => true,
	    'action' => 'register',
	    'class' => 'participate',
	    'css' => true,
	    'reset_params' => true,
	    'route' => 'main-module',
	    'pages' => array(
	    	array(
	    	    'label' => 'Conduct',
	    	    'title' => 'Conduct',
				'visible' => true,
	    	    'module' => 'web',
	    	    'controller' => 'participate',
	    	    'action' => 'conduct',
	    		'threeColumnLayout' => true,
	    		'visible' => false
	    	),
	    	array(
	    		'label' => 'Submissions',
	    		'title' => 'List of submitted papers',
				'visible' => true,
	    		'module' => 'core',
	    		'controller' => 'submit',
	    		'resource' => 'Submit', // ACL
	    		'action' => ($acl->isAllowed($role, 'Submit', 'list')) ? 'list' : 'new',
	    		'reset_params' => true,	    		
	    		'route' => 'main-module',
	    		'privilege' => ($acl->isAllowed($role, 'Submit', 'list')) ? 'list' : 'new'
	    	),
	    	array(
	    		'label' => 'Review',
	    		'title' => 'Review',
	    		'module' => 'core',
	    		'controller' => 'review',
	    		'resource' => 'Review', // ACL
	    		'action' => 'listmine',
	    		'privilege' => 'listmine',	
	    		'route' => 'main-module',
	    		'reset_params' => true
	    	),
	    	array(
	    	    'label' => 'Guidelines',
	    	    'title' => 'guidelines',
	    	    'module' => 'web',
	    	    'controller' => 'participate',
	    	    'action' => 'guidelines',
	    	),	
	    	array(
	    		'label' => 'Topics',
	    		'title' => 'Topics',
				'visible' => false,
	    		'module' => 'core',
	    		'controller' => 'topic',
	    		'route' => 'main-module',
	    		'action' => 'list',
	    		'reset_params' => true
	    	),
	    	array(
	    	    'label' => 'Register',
	    	    'title' => 'register',
				'visible' => true,
	    	    'module' => 'web',
	    	    'controller' => 'participate',
	    	    'action' => 'register',
	    	),
            array(
                    'label' => 'Students',
                    'title' => 'student',
					'visible' => false,
                    'module' => 'web',
                    'controller' => 'participate',
                    'action' => 'student',
            ),
	    	array(
	    	    'label' => 'Participants',
	    	    'title' => 'participants',
				'visible' => true,
	    	    'module' => 'web',
	    	    'controller' => 'participate',
	    	    'action' => 'participants',
	    		'threeColumnLayout' => true
	    	),	    	
	    	array(
	    	    'label' => 'Booth Schedule',
	    	    'title' => 'Booth Schedule',
				'visible' => false,
	    	    'module' => 'web',
	    	    'controller' => 'participate',
	    	    'action' => 'terenabooth',
	    		'threeColumnLayout' => true
	    	)
	    )
	)
);
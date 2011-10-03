<?php
return array(
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
			   'visible' => false,
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
	    	'visible' => false,
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
	    	'visible' => false,
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
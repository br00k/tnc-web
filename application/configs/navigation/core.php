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
 * @revision   $Id: core.php 41 2011-11-30 11:06:22Z gijtenbeek@terena.org $
 */


/**
 *
 * @note Submissions are only visible if you are signed in. Your role should be at least 'user'.
 * When you are a user the 'submit' navigation button links to 'submit::new' action.
 * When you are allowed to list submissions, 'submit' navigation button links to 'submit::list' action
 */

return array(
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
			    'reset_params' => true,
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
			),
			array(
	    		'label' => 'Submissions',
	    		'title' => 'List of submitted papers',
	    		'module' => 'core',
	    		'visible' => ($view->conferenceInfo()->isSubmitLive()) ? true : false,
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
	    		'action' => 'listmine',
	    		'route' => 'main-module',
	    		'reset_params' => false,
	    		'privilege' => 'listmine',
	    		'reset_params' => true
	    	)

);
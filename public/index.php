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
	 */

// Needed for Location header, which needs an absolute URI (RFC2616)
$proto = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';

// Define path to application directory
defined('APPLICATION_PATH')
	|| define('APPLICATION_PATH', realpath(dirname(__FILE__).'/../application'));

// Check for application.ini, which should have created by the installer.
// Not found? Then assume we are not installed, and run the installer.
$ini_file = APPLICATION_PATH.'/configs/application.ini';
if (!$ini = parse_ini_file($ini_file)) {
	header('Location: '.$proto.$_SERVER['SERVER_NAME'].'/installer');
	exit;
}

if (!isset($ini['zend.location'])) {
	echo "'zend.location' not set in $ini_file";
	exit;
}

// Define application environment
defined('APPLICATION_ENV')
	|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));


$paths = array(
	get_include_path(),
	dirname(dirname(__FILE__)).'/library',
	$ini['zend.location'],
);

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, $paths));

// Zend_Application
require_once $ini['zend.location'].'/Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, $ini_file);
$application->bootstrap()
			->run();
?>

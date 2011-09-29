<?php
/*
=====================================================
PHP Setup Wizard Script - by VLD Interactive
----------------------------------------------------
http://www.phpsetupwizard.com/
http://www.vldinteractive.com/
-----------------------------------------------------
Copyright (c) 2005-2011 VLD Interactive
=====================================================
THIS IS COPYRIGHTED SOFTWARE
PLEASE READ THE LICENSE AGREEMENT
http://www.phpsetupwizard.com/license/
=====================================================
*/
ini_set('display_errors', 'on');
error_reporting(E_ALL);

// Needed for Location header, which needs an absolute URI (RFC2616)
$proto = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';


$base_path = str_replace('\\', '/', realpath(dirname(__FILE__))).'/';
$virtual_path = str_replace('\\', '/', $proto . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF'])).'/';
define('BASE_PATH', $base_path);
define('VIRTUAL_PATH', $virtual_path);
define('CORE_DIR', dirname(dirname(BASE_PATH)));

// Need an empty config file to start with (created manually by touching is - see docs)
$ini_file = '../../application/configs/application.ini';
if(!is_file($ini_file)) {
	echo $ini_file. ' does not exist';
	exit;
}

$ini = @parse_ini_file($ini_file);
// If zend.location in found, assume we're installed, and redirect to app
if(isset($ini['zend.location'])) {
	header('Location: '.$proto.$_SERVER['SERVER_NAME']);
	exit;
}
	

include BASE_PATH . 'includes/core/wizard.php';
include BASE_PATH . 'includes/wizard.php';

//var_dump(BASE_PATH); var_dump(VIRTUAL_PATH);

$wizard = new phpSetupWizard();

$wizard->run();

//echo "<pre>Session info:<br />"; var_dump($_SESSION); echo "Reqeust info<br />"; var_dump($_REQUEST); var_dump(BASE_PATH); var_dump(VIRTUAL_PATH);


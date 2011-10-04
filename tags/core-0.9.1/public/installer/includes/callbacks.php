<?php

/**
 * Callbacks class
 */
require_once('functions.php');
class Callbacks extends Callbacks_Core
{
	function zend_check($params=array())
	{
		//echo "asdfas"; var_dump($params);
		if(isset($params['location'])) {
			if (!@include_once($params['location'].'/Zend/Version.php')) {
				$this->error = 'Zend Framework not found in '.$params['location'];
				return false;
			}

			if(Zend_Version::compareVersion($params['version']) == '1') {
				$this->error = 'Zend Framework found, but version '.Zend_Version::VERSION.' is too old.<br />
					Need at least version '.$params['version'].'.';
				return false;
			}
		}
		// Version equal or greater than what we want, good!
		return true;
	}


	/**
	 * Checks for a software code tree, and if found, which version is there
	 */
	function simplesamlphp_check($loc)
	{
		// No version checks here, because there is no way of determining the version of SimpleSAMLphp.
		if(isset($loc)) {
			if (!is_readable($loc.'/lib/SimpleSAML/Utilities.php')) {
				$this->error = 'SimpleSAMLphp not found in '.$loc;
				return false;
			} else {
				return true;
			}
		}
	}


/*	function simplesamlphp_auth_check($params=array()) {
		if(require_once($params['ssp_location'].'/lib/_autoload.php')) {
			$as = new SimpleSAML_Auth_Simple($params['as']);
			$as->requireAuth();
			$attributes = $as->getAttributes();
			$this->error = print_r(array_keys($attributes), 1);
			return false;
		} else {
			return false;
		}
	}
*/

	function install($attributes=array())
	{
		// Initialise $conf items by putting a banner in first
		$conf = array('banner'=> "This file was automagically created by the {$this->config['title']} installer wizard on ".date('c'));

		// Put all non-empty session params into $conf
		foreach($_SESSION['params'] as $key=>$val) {
			if(!empty($val)) {
				$conf[$key] = $val;
			}
		}
		
		// use only 'saml' as this contains real attributes;
		$attributes = $attributes['saml'];
	//echo "<pre>";	var_dump($conf); var_dump($attributes); exit;



		// Database check
		$dbconf = array(
				'db_host' => $conf['db_hostname'],
				'db_user' => $conf['db_username'],
				'db_pass' => $conf['db_password'],
				'db_name' => $conf['db_name'],
				);
		if ( !$this->db_init($dbconf) ) {
			return false;
		}



		// Import SQL skeleton and basic data
		if ( !$this->db_import_file(BASE_PATH.'sql/data.sql')) {
			return false;
		}

		// Initialise Conference
		$conf_abbr = $this->db_escape($conf['conf_abbr']);
		$conf_name = $this->db_escape($conf['conf_name']);
		$conf_hostname = $this->db_escape($conf['conf_hostname']);
		if(!$this->db_query("INSERT INTO conferences (abbreviation, name, hostname) VALUES ($conf_abbr, $conf_name, $conf_hostname)")) {
			return false;
		}

echo "asfasf";
       // Add administrative account
        $admin_uid = $this->db_escape($attributes[$conf['ssp_uid_attribute']][0]);
        $admin_fname = $this->db_escape($attributes[$conf['ssp_fname_attribute']][0]);
        $admin_lname = $this->db_escape($attributes[$conf['ssp_lname_attribute']][0]);
        $admin_email = $this->db_escape($attributes[$conf['ssp_email_attribute']][0]);
        $admin_organisation = $this->db_escape($attributes[$conf['ssp_organisation_attribute']][0]);
        $admin_country = $this->db_escape($attributes[$conf['ssp_country_attribute']][0]);

	echo "yes";
        $admin_query = "INSERT INTO users (uid, fname, lname, email, organisation, country) VALUES ($admin_uid, $admin_fname, $admin_lname, $admin_email, $admin_organisation, $admin_country)";
        if(!$this->db_query($admin_query)) {
			return false;
		}

		if(!$this->db_query("INSERT INTO user_role (user_id, role_id) VALUES (1, 999)")) {
			return false;
		}






// Not needed with PDO any more
//		$this->db_close();



		//Generate application.ini config file
		$config_template = './templates/application_ini.tpl';
		if(!$new_config = @file_get_contents($config_template)) {
			$this->error = "Could not load config template at ". realpath($config_template);
			return false;
		}

		// Replace every instance of {blah} with the value of $conf['blah']
		$new_config = preg_replace('/{(.*?)}/e', '$conf["$1"]', $new_config);

		//		echo "<pre>";var_dump($new_config); $this->error = "hacked"; return false;
		if(!@file_put_contents('../../application/configs/application.ini', $new_config)) {
			$e = error_get_last();
			$this->error = $e['message'];
			return false;
		}


/*
		// Generate index.php, with Zend path
		$index_template = './templates/index_php.tpl';
		if(!$new_index = @file_get_contents($index_template)) {
			$this->error = "Could not load template at ".realpath($index_template);
			return false;
		}
		// Replace every instance of {blah} with the value of $conf['blah']
		$new_index = preg_replace('/{(.*?)}/e', '$conf["$1"]', $new_index);

		if(!@file_put_contents('../index.php', $new_index)) {
			$e = error_get_last();
			$this->error = $e['message'];
			return false;
		}
*/


		// .htaccess
		$htaccess_tpl = './templates/htaccess_tpl';
		if(!$new_htaccess = @file_get_contents($htaccess_tpl)) {
			$this->error = "Could not load htaccess template from $htaccess_tpl";
			return false;
		}

		// Replace every instance of {blah} with the value of $conf['blah']
		$new_htaccess = preg_replace('/{banner}/e', '$conf["banner"]', $new_htaccess);

		if(!@file_put_contents('../.htaccess', $new_htaccess)) {
			$e = error_get_last();
			$this->error = $e['message'];
			return false;
		}

		// Needed for Location header, which needs an absolute URI (RFC2616)
		$proto = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';

		// Hack to redirect upon sucess
		header('Location: '.$proto.$_SERVER['SERVER_NAME']);
		return true;
	}

}

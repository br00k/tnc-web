<?php

$host = $_SERVER["HTTPS"] == "on" ? "https" : "http";
$host .= '://'.$_SERVER['SERVER_NAME'];


/*
   Can file be included, and if so, what is the path of it?
 */
function file_path($file) {
	if(@include_once($file)) {
		foreach(explode(PATH_SEPARATOR,get_include_path()) as $p) {
			if(is_readable($p.DIRECTORY_SEPARATOR.$file)) {
				return $p;
			}
		}
	}
	return false;
}

function mydomain() {
	$labels = explode('.', $_SERVER['HTTP_HOST']);
	if( $domain = implode('.', array_slice($labels, -2))) {
		return $domain;
	} else {
		return 'some.domain';
	}
}

function issetweb($varname) {
	// Try to find $var in session, or in reqeust
	if(isset($_SESSION['params'][$varname])) {
		return $_SESSION['params'][$varname];
	} elseif(isset($_REQUEST[$varname])) {
		return $_REQUEST[$varname];
	} else {
		return null;
	}
}

/*
 * Find available authsources in existing SimpleSAMLphp install
 * Returns array with equal key/values.
 */
function get_authsources($dir=null) {
	if(!isset($dir)) {
		if(isset($_REQUEST['ssp_location'])) {
			$dir = $_REQUEST['ssp_location'];
		} elseif(isset($_SESSION['params']['ssp_location'])) {
			$dir = $_SESSION['params']['ssp_location'];
		} else {
			return false;
		}
	}
	$authsources = array();
	$configfile = realpath($dir.'/config/authsources.php');
	if(@include_once($configfile)) {
		if(!empty($config)) {
			foreach(array_keys($config) as $as) {
				$authsources[$as] = $as;
			}
			ksort($authsources);
			return $authsources;
		}
	}
	return false;
}



function get_attributes() {
	// Only run in step 5 or later ! So change when steps array is changed!
	if(isset($_REQUEST['s'])) {
		if($_REQUEST['s'] >= 4) {
			if($ssp_location = issetweb('ssp_location')) {
				$ssp_autoloader = $ssp_location.'/lib/_autoload.php';
				if(is_readable($ssp_autoloader)) {
					//echo "<pre>sesion:"; var_dump($_SESSION); echo "rquest"; var_dump($_REQUEST);
					include_once($ssp_autoloader);
					if($ssp_authsource = issetweb('ssp_authsource')) {
						$as = new SimpleSAML_Auth_Simple($ssp_authsource);
						if(!$as->isAuthenticated()) {
							$as->requireAuth();
						}
						$attributes = $as->getAttributes();
						foreach(array_keys($attributes) as $at) {
							// These are key|value pairs to populate the SELECT boxes
							$simpleattrs[$at] = $at. " (". $attributes[$at][0]. ")";
						}
						// Add attributes themselves as well, for later use
						$simpleattrs['saml'] = $attributes;
						//	echo "<pre>"; var_dump($simpleattrs);
						ksort($simpleattrs);
						return $simpleattrs;
					}
				}
			}
		}
	}
	return false;
}


function get_admin() {
	if($a = get_attributes()) {
		if($admin = issetweb('ssp_uid_attribute')) {
			return $a['saml'][$admin][0];
		}
	}
	return false;
}

?>

<?php

/**
* Validation class
*/
class Validation extends Validation_Core
{
	// These function are part of the default $steps array to help you
	// illustrate how this script works. Feel free to delete them.

	function validate_license($params = array())
	{
		if (strcmp('1234-1234-1234-1234', $_SESSION['params']['license_number']) != 0) {
			return false;
		}
		return true;
	}

	function validate_system_path($params = array())
	{
		if (!is_file(rtrim($_SESSION['params']['system_path'], '/').'/sample_config.php') || !is_writable(rtrim($_SESSION['params']['system_path'], '/').'/sample_config.php')) {
			$this->error = rtrim($_SESSION['params']['system_path'], '/').'/sample_config.php file does not exist or is not writeable.';
			return false;
		}

		return true;
	}
}

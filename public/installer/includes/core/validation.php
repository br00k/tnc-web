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

/**
* Validation core class
*/
class Validation_Core
{
	var $config = array();
	var $language = array();
	var $error = false;

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array
	 * @param	array
	 */
	function Validation_Core($config, $language)
	{
		$this->config = $config;
		$this->language = $language;
	}

	/**
	 * MySQL database
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function database($field)
	{
		#$this->error = "asdfasdf"; return false;
		if (!is_array($field['params']) || !isset($field['params']['db_host']) || !isset($field['params']['db_user']) || !isset($field['params']['db_pass']) || !isset($field['params']['db_name'])) {
			$this->error = $this->language['db_params'];
			return false;
		}

		$db_host = isset($_POST[$field['params']['db_host']]) ? $_POST[$field['params']['db_host']] : '';
		$db_user = isset($_POST[$field['params']['db_user']]) ? $_POST[$field['params']['db_user']] : '';
		$db_pass = isset($_POST[$field['params']['db_pass']]) ? $_POST[$field['params']['db_pass']] : '';
		$db_name = isset($_POST[$field['params']['db_name']]) ? $_POST[$field['params']['db_name']] : '';

		if ($this->config['db_type'] == 'mysql') {
			$link = @mysql_connect($db_host, $db_user, $db_pass);
		}
		elseif ($this->config['db_type'] == 'mysqli') {
			$link = @mysqli_connect($db_host, $db_user, $db_pass);
		}
		elseif ($this->config['db_type'] == 'pgsql') {
			#echo "<pre>".print_r($this->config, 1)."</pre>";
			if ($db_name) {
				#$link = pg_connect("host=$db_host dbname=$db_name user=$db_user password=$db_pass");
				try {
					$dbh = new PDO("pgsql:dbname=$db_name;host=$db_host", $db_user, $db_pass);
				}
				catch (PDOException $e) {
					$this->error = $e->getMessage();
					return false;
				}
				return true;
			}
		} else {
			$this->error = $this->language['db_select'];
			return false;
		}

		if (!$link) {
			$this->error = $this->language['db_connect'];
			return false;
		}

		if ($db_name) {
			if ($this->config['db_type'] == 'mysql') {
				$db = @mysql_select_db($db_name, $link);
			}
			elseif ($this->config['db_type'] == 'mysqli') {
				$db = @mysqli_select_db($link, $db_name);
			}

			if (!$db) {
				$this->error = $this->language['db_select'];
				return false;
			}
		}

		return true;
	}

	/**
	 * Is folder or file writable
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function is_writable($field)
	{
		@clearstatcache();

		if (!@is_writable($field['value'])) {
			return false;
		}

		return true;
	}

	/**
	 * Required
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function required($field)
	{
		if (is_array($field['value'])) {
			return $field['value'] ? true : false;
		} else {
			return $field['value'] != '' ? true : false;
		}
	}

	/**
	 * Match one field to another
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function matches($field)
	{
		if (!isset($_POST[$field['params']])) {
			return false;
		}

		if (is_array($field['params'])) {
			$field['params'] = current($field['params']);
		}

		return $field['value'] === $_POST[$field['params']] ? true : false;
	}

	/**
	 * Minimum length
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function min_length($field)
	{
		if (function_exists('mb_strlen')) {
			return mb_strlen($field['value']) < $field['params'] ? false : true;
		}

		return strlen($field['value']) < $field['params'] ? false : true;
	}

	/**
	 * Max length
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function max_length($field)
	{
		if (function_exists('mb_strlen')) {
			return mb_strlen($field['value']) > $field['params'] ? false : true;
		}

		return strlen($field['value']) > $field['params'] ? false : true;
	}

	/**
	 * Exact Length
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function exact_length($field)
	{
		if (function_exists('mb_strlen')) {
			return mb_strlen($field['value']) != $field['params'] ? false : true;
		}

		return strlen($field['value']) != $field['params'] ? false : true;
	}

	/**
	 * Minimum value
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function min_value($field)
	{
		if (preg_match('#/[^0-9]#', $field['value'])) {
			return false;
		}

		return $field['value'] < $field['params'] ? false : true;
	}

	/**
	 * Max value
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function max_value($field)
	{
		if (preg_match('#/[^0-9]#', $field['value'])) {
			return false;
		}

		return $field['value'] > $field['params'] ? false : true;
	}

	/**
	 * Exact value
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function exact_value($field)
	{
		return $field['value'] !== $field['params'] ? false : true;
	}

	/**
	 * Valid email
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function valid_email($field)
	{
		return (boolean) preg_match('#^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$#ix', $field['value']);
	}

	/**
	 * Valid emails
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function valid_emails($field)
	{
		if (strpos($field['value'], ',') === false) {
			return $this->valid_email($field);
		}

		foreach (explode(',', $field['value']) as $email) {
			if ($this->valid_email(trim($email)) === false) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Validate ip address
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function valid_ip($field)
	{
		$segments = explode('.', $field['value']);

		if (count($segments) != 4) {
			return false;
		}

		if ($segments[0][0] == '0') {
			return false;
		}

		foreach ($segments as $segment) {
			if ($segment == '' OR preg_match("/[^0-9]/", $segment) OR $segment > 255 OR strlen($segment) > 3) {
				return false;
			}
		}

		return true;
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function alpha($field)
	{
		return (boolean) preg_match('#^([a-z])+$#i', $field['value']);
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha numeric
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function alpha_numeric($field)
	{
		return (boolean) preg_match('#^([a-z0-9])+$#i', $field['value']);
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha numeric with underscores and dashes
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function alpha_dash($field)
	{
		return (boolean) preg_match('#^([-a-z0-9_-])+$#i', $field['value']);
	}

	// --------------------------------------------------------------------

	/**
	 * Numeric
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function numeric($field)
	{
		return (boolean) preg_match('#^[\-+]?[0-9]*\.?[0-9]+$#', $field['value']);

	}

	// --------------------------------------------------------------------

	/**
	 * Is numeric
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
  	function is_numeric($field)
	{
		return is_numeric($field['value']) ? true : false;
	}

	// --------------------------------------------------------------------

	/**
	 * Integer
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function integer($field)
	{
		return (boolean) preg_match('#^[\-+]?[0-9]+$#', $field['value']);
	}

	// --------------------------------------------------------------------

	/**
	 * Is a natural number (0,1,2,3, etc)
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function is_natural($field)
	{
   		return (boolean) preg_match('#^[0-9]+$#', $field['value']);
	}

	// --------------------------------------------------------------------

	/**
	 * Is a natural number, but not a zero (1,2,3, etc)
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function is_natural_no_zero($field)
	{
		if (!preg_match('#^[0-9]+$#', $field['value'])) {
			return false;
		}

		if ($field['value'] == 0) {
			return false;
		}

		return true;
	}

	/**
	 * Calls native PHP function
	 *
	 * @access	public
	 * @param	array
	 * @return	boolean
	 */
	function php_function($field)
	{
		if (!is_array($field['params'])) {
			$field['params'] = array($field['params']);
		}

		return call_user_func_array($field['function'], $field['params']);
	}
}

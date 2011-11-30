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
 * @revision   $Id$
 */

/**
 * Identical field validator
 *
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 * @package TA_Form
 * @subpackage Validator
 */
class TA_Form_Validator_IdenticalFields extends Zend_Validate_Abstract
{

	const NO_MATCH = 'noMatch';

	/**
	 * The fields that the current field needs to match
	 *
	 * @var array
	 */
	private $_repeatedField = array();
	
	protected $_messageTemplates = array(
		self::NO_MATCH => "Values do not match"
	);
	
	public function __construct($repeatedField)
	{
		$this->_repeatedField = $repeatedField;
	}

	public function isValid($value, $context = null)
	{
		$value = (string) $value;
		$this->_setValue($value);

		if (is_array($context)) {
	        if ( isset($context[$this->_repeatedField]) && ($value == $context[$this->_repeatedField]) ) {
	            return true;
	        }
        } elseif (is_string($context) && ($value == $context)) {
        	return true;
    	}

    	$this->_error();
    	return false;

	}



}
<?php

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
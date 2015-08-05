<?php

class TA_Form_Validator_DateIsLater extends Zend_Validate_Abstract
{

	const NOT_LATER = 'notLater';

	/**
	 * The fields that the current field needs to be compared to
	 *
	 * @var string
	 */
	private $_field;

	protected $_messageTemplates = array(
		self::NOT_LATER => "End date should be after start date"
	);

	public function __construct($field)
	{
		$this->_field = $field;
	}

	public function isValid($value, $context = null)
	{
		$value = (string) $value;
		$this->_setValue($value);

		$endDate = new Zend_Date($value);

		if ( isset($context[$this->_field]) && $endDate->isLater($context[$this->_field]) ) {
			return true;
		} else {
			$this->_error();
			return false;
		}

	}
}
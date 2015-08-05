<?php

class TA_Form_Validator_Timeslot extends Zend_Validate_Abstract
{
	const NOT_EMPTY = 'notempty';

	const DATE_MISMATCH = 'dateMismatch';

	protected $_messageTemplates = array(
		self::NOT_EMPTY => "Date values can't be empty",
		self::DATE_MISMATCH => "Start date can't be later than end date"

	);

	public function isValid($value, $context = null)
	{

		$this->_setValue($value);

        if ( ($value['tstart'] == '') || ($value['tend'] == '') ) {
            $this->_error(self::NOT_EMPTY);
           	return false;
        }

		$start = new Zend_Date($value['tstart']);
		$end = new Zend_Date($value['tend']);

		if ( $end->isEarlier($start) ) {
		   $this->_error(self::DATE_MISMATCH);
		   return false;
		}

    	return true;

	}

}
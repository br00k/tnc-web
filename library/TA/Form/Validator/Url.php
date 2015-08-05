<?php
/**
 * Custom Url validator - uses Zend_Uri to check validity of URL
 * Zend_Uri can't be used in a Form since it does not implement the
 * Zend_Validate_Interface. This custom class allows you to use the functionality.
 * Ex: $formElement->addValidator('url');
 */

class TA_Form_Validator_Url extends Zend_Validate_Abstract
{
    const INVALID_URL = 'invalidUrl';

    protected $_messageTemplates = array(
        self::INVALID_URL   => "Please provide a valid URL.",
    );

    public function isValid($value)
    {
        $valueString = (string) $value;
        $this->_setValue($valueString);

        if (!Zend_Uri::check($value)) {
            $this->_error(self::INVALID_URL);
            return false;
        }
        return true;
    }
}

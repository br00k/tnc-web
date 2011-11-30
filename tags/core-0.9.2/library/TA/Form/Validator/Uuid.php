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
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */  
class TA_Form_Validator_Uuid extends Zend_Validate_Abstract 
{ 
    /**
     * Validation failure message key for when the value contains non-alphabetic
     * or non-digit characters
     */ 
    const NOT_UUID = 'notUuid'; 
 
    /**
     * Validation failure message key for when the value is an empty string
     */ 
    const STRING_EMPTY = 'stringEmpty'; 
 
    /**
     * Validation failure message template definitions
     *
     * @var array
     */ 
    protected $_messageTemplates = array( 
        self::NOT_UUID     => "'%value%' is not a valid UUID", 
        self::STRING_EMPTY => "'%value%' is an empty string" 
    ); 
 
    /**
     * Sets default option values for this instance
     *
     * @return void
     */ 
    public function __construct() 
    { 
    } 
 
    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value contains a valid UUID
     *
     * @param  string $value
     *
     * @return boolean
     */ 
    public function isValid($value) 
    { 
        $value = (string) $value; 
 
        $this->_value = $value; 
 
        // check if string is empty 
        if (!strlen($value)) { 
            $this->_error(self::STRING_EMPTY); 
            return false; 
        } 
 
        if (function_exists('uuid_is_valid') && !uuid_is_valid($value)) 
        { 
            $this->_error(self::NOT_UUID); 
            return false; 
        } 
 
        // check length 
        if (strlen($value) !== 36) { 
            $this->_error(self::NOT_UUID); 
            return false; 
        } 
 
        // are there some invalid characters 
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i'; 
        if (!preg_match($pattern, $value)) { 
            $this->_error(self::NOT_UUID); 
            return false; 
        } 
 
        return true; 
    } 
}
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
 * @revision   $Id: Timeslot.php 598 2011-09-15 20:55:32Z visser $
 */

/**
 * CORE timeslot validator
 *
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 * @package TA_Form
 * @subpackage Validator
 */
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
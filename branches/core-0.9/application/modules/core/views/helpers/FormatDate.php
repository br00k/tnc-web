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
 * @revision   $Id: FormatDate.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */

/**
 * Date View helper
 *
 * @package Core_View_Helper
 */
class Core_View_Helper_FormatDate extends Zend_View_Helper_Abstract
{

	/**
	 * Format a date based on locale and timezone
	 *
	 * @param	string	$date		Timestamp
	 * @param	string	$timezone	Timezone to use
	 * @param	string	$format		Format to output date in
	 */
	public function formatDate($date, $timezone = null, $format = null)
	{
		if (!$date) {
			return false;
		}

		$zendDate = new Zend_Date(
		    $date,
		    Zend_Date::ISO_8601,
		    Zend_Registry::get('Zend_Locale')
		);
		if ($timezone) {
			$zendDate->setTimezone($timezone);
		}

		if ($format) {
			return $zendDate->get($format);
		}

		return $zendDate;

	}

}
<?php

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
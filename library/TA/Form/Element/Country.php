<?php
/**
 * Custom Form Element for countries
 *
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class TA_Form_Element_Country extends Zend_Form_Element_Select
{
	public function init()
	{
	    $this->setMultiOptions(
			 	$this->_getCountries()
			 );
	}

	/**
	 * Get a localized list of countries sorted by country name
	 *
	 * @return array
	 */
	private function _getCountries()
	{
		$countries = (Zend_Locale::getTranslationList('territory', null, 2));
		asort($countries, SORT_LOCALE_STRING);
		
		array_unshift($countries, '---');
		return $countries;
	}
}

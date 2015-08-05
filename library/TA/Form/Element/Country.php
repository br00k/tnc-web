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
 * @revision   $Id: Country.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */
 
/**
 * Custom Form Element for countries
 *
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 * @package TA_Form
 * @subpackage Element
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

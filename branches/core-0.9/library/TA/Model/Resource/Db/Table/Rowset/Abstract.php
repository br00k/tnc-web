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
 * @revision   $Id: Abstract.php 598 2011-09-15 20:55:32Z visser $
 */

class TA_Model_Resource_Db_Table_Rowset_Abstract extends Zend_Db_Table_Rowset_Abstract {

	/**
	 * Return column/value pairs with date values
	 * transformed to Zend_Date objects
	 *
	 * @param string $dateFormat format to output the date to
	 * @return array
	 */
	public function toMagicArray($dateFormat = null)
	{
		$metadata = $this->getTable()->info('metadata');

		$timestampFields = array_filter($metadata, function($val) {
			return $val['DATA_TYPE'] == 'timestamptz';
		});

		foreach ($this as $row) {
			foreach (array_keys($timestampFields) as $field) {
				if ($row->$field) {
					$row->$field = $this->_isoToNormalDate($row->$field, $dateFormat);
				}
			}
		}

		return $this->toArray();
	}

	/**
	 * Transforms DB timestamp to normal date
	 *
	 * @param string $value timestamp to transform
	 * @param string $dateFormat format to transform the timestamp to
	 *
	 * @return object Zend_Date
	 * @todo Made this a seperate method so I can call this from parent class
	 */
	protected function _isoToNormalDate($value, $dateFormat = null)
	{
		$zendDate = new Zend_Date(
		    $value,
		    Zend_Date::ISO_8601,
		    Zend_Registry::get('Zend_Locale') //@todo: it seems I can remove this, because it gets it automatically
		);
		if ($dateFormat) {
			return $zendDate->get($dateFormat);
		}

		return $zendDate;
	}

}
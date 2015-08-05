<?php

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
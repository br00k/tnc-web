<?php
class Core_Resource_Eventcategories extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'eventcategories';

	protected $_primary = 'eventcategory_id';

	public function init() {}

	/**
	 * Get event categories
	 *
	 * @return	array
	 */
	public function getCategories($empty = null)
	{
		$return = array();

		$return = $this->getAdapter()->fetchPairs($this->select()
			->from($this->info('name'), array('eventcategory_id', 'category'))
			->order('category ASC')
		);

		if ($empty) {
			$return[0] = $empty;
			asort($return);
		}

		return $return;
	}
}
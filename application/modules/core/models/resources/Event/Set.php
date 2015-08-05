<?php
/**
 * This class represents a rowset
 */
class Core_Resource_Event_Set extends Zend_Db_Table_Rowset_Abstract
{
	/**
	 * Group all events by day
	 *
	 * @param	string	$by		Group by? day/category
	 * @return array
	 */
	public function group($by = 'day')
	{
		$list = array();
		
		$by = strtolower($by);

		$values = $this->toArray();

		foreach ($this as $row) {
			switch ($by) {
				case 'day':
					$start = new Zend_Date($row->tstart);
					$list[$start->get('dd/MM/yyyy')][] = $row;
				break;
				case 'category':
					$list[$row->category][] = $row;
				break;
			}
		}
		// sort by array key
		ksort($list);
		return $list;
	}

}
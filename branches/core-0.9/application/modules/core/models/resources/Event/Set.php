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
 * @revision   $Id: Set.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */
 
/** 
 *
 * @package Core_Resource
 * @subpackage Core_Resource_Event
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
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
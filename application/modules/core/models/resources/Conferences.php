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
 * @revision   $Id$
 */

class Core_Resource_Conferences extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'conferences';

	protected $_primary = 'conference_id';

	protected $_rowClass = 'Core_Resource_Conference_Item';

	public function init() {}

	/**
	 * Gets conference by primary key
	 *
	 * @return object Core_Resource_Conference_Item
	 */
	public function getConferenceById($id)
	{
		return $this->find( (int)$id )->current();
	}

	/**
	 * Gets conference by hostname
	 *
	 * @param	string	$hostname
	 * @return	object	Core_Resource_Conference_Item
	 */
	public function getConferenceByHostname($hostname)
	{
		return $this->fetchRow(
					$this->select()
					->where('hostname = ?', $hostname)
				);
	}

	/**
	 * Gets list of conferences
	 *
	 * @return array
	 */
	public function getConferences($paged=null, $order=array())
	{
		$grid = array();
		$grid['cols'] = $this->getGridColumns();
		$grid['primary'] = $this->_primary;

		$select = $this->select();

		if (!empty($order[0])) {
			$order = 'lower('.$order[0].') '.$order[1];
		} else {
			$order = 'lower(name) ASC';
		}
		$select->order($order);

		$select->from( 'conferences', array_keys($this->getGridColumns()) );

		if ($paged) {

			$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);

			$paginator = new Zend_Paginator($adapter);
			$paginator->setCurrentPageNumber( (int)$paged )
					  ->setItemCountPerPage(20);

			$grid['rows'] = $paginator;
			return $grid;
		}

		$grid['rows'] = $this->fetchAll($select);
		return $grid;

	}

	/**
	 * Convenience method to get grid columns
	 *
	 * @return array
	 * @todo in php5.3 I can add lambda's for modifiers
	 */
	private function getGridColumns()
	{
		return array(
			// conference_id is hidden so I don't have to provide a label
			'conference_id' => array('field' => 'conference_id', 'sortable' => true, 'hidden' => true),
			'name' => array('field' => 'name', 'label' => 'Name', 'sortable' => true),
			'abbreviation' => array('field' => 'abbreviation', 'label' => 'Abbreviation', 'sortable' => true),
			'email' => array('field' => 'email', 'label' => 'Email', 'sortable' => true),
			'hostname' => array('field' => 'hostname', 'label' => 'Hostname', 'sortable' => false, 'hidden' => true)
		);
	}

	/**
	 * Create default timeslot values based on days/session duration
	 *
	 * @param	array	$post	Post request
	 */
	public function createTimeslots($post)
	{
		$db = $this->getAdapter();

		$dt = new Zend_Date($post['start'], 'dd/MM/YYYY hh:mm');

		$sql = 'insert into timeslots (tstart, tend, number, type, conference_id)' .
			   'values (:tstart, :tend, :number, :type, '. (int) $post['id'] .')';

		$stmt = new Zend_Db_Statement_Pdo($db, $sql);

		$n = 0;
		// for every day add slot/break/lunch/slot/break/slot
		for ($i = 1; $i <= $post['days']; $i++) {

			$stmt->execute(array(
			    ':tstart' => $dt->get(Zend_Date::ISO_8601),
			    ':tend' => $dt->add('90', 'mm')->get(Zend_Date::ISO_8601),
			    ':number' => $n+1,
			    ':type' => 1
			));

			$stmt->execute(array(
			    ':tstart' => $dt->get(Zend_Date::ISO_8601),
			    ':tend' => $dt->add('30', 'mm')->get(Zend_Date::ISO_8601),
			    ':number' => 0,
			    ':type' => 2
			));

			$stmt->execute(array(
			    ':tstart' => $dt->get(Zend_Date::ISO_8601),
			    ':tend' => $dt->add('90', 'mm')->get(Zend_Date::ISO_8601),
			    ':number' => $n+2,
			    ':type' => 1
			));

			$stmt->execute(array(
			    ':tstart' => $dt->get(Zend_Date::ISO_8601),
			    ':tend' => $dt->add('90', 'mm')->get(Zend_Date::ISO_8601),
			    ':number' => 0,
			    ':type' => 3
			));

			$stmt->execute(array(
			    ':tstart' => $dt->get(Zend_Date::ISO_8601),
			    ':tend' => $dt->add('90', 'mm')->get(Zend_Date::ISO_8601),
			    ':number' => $n+3,
			    ':type' => 1
			));

			$stmt->execute(array(
			    ':tstart' => $dt->get(Zend_Date::ISO_8601),
			    ':tend' => $dt->add('30', 'mm')->get(Zend_Date::ISO_8601),
			    ':number' => 0,
			    ':type' => 2
			));

			$stmt->execute(array(
			    ':tstart' => $dt->get(Zend_Date::ISO_8601),
			    ':tend' => $dt->add('90', 'mm')->get(Zend_Date::ISO_8601),
			    ':number' => $n = $n+4,
			    ':type' => 1
			));

			// reset date and add a day
			$dt = new Zend_Date($post['start'], 'dd/MM/YYYY hh:mm');
			$dt->add($i, Zend_Date::DAY_SHORT);

		}

		return true;
	}


}


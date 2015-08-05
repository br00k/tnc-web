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
 * @revision   $Id: Timeslots.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Timeslots extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'timeslots';

	protected $_primary = 'timeslot_id';

	protected $_rowClass = 'Core_Resource_Timeslot_Item';

	public function init()
	{
		parent::init();
	}

	/**
	 * Gets timeslot by primary key
	 * @return object Core_Resource_Timeslot_Item
	 */
	public function getTimeslotById($id)
	{
		return $this->find( (int)$id )->current();
	}

	/**
	 * Get grouped timeslots of certain type
	 *
	 * @param	mixed		$type			integer for specific type 
	 * 										(presentation/break/lunch etc.) or false for all
	 * @param	integer		$conferenceId	conference_id
	 */
	public function getTimeslotsForSelect($type = false, $conferenceId = null)
	{
		$return = array();

		$timeslots = $this->getTimeslots($type, $conferenceId);

		foreach ($timeslots as $slot) {
			$return[$slot->getStartDay()][$slot->timeslot_id] = $slot->getCompleteTimeslot();
		}

		return $return;
	}


	/**
	 * Get timeslots of certain type belonging to a conference
	 *
	 * @param	mixed		$type			integer for specific type or false for all
	 * @param	integer		$conferenceId	conference_id
	 * @return	Zend_Db_Table_Rowset	rows ordered by start time
	 */
	public function getTimeslots($type = false, $conferenceId = null)
	{
		$conferenceId = ($conferenceId) ? $conferenceId : $this->getConferenceId();

		$select = $this->select()
			->where('conference_id = ?', (int) $conferenceId)
			->order('tstart ASC');

		if ($type) {
			$select->where('type = ?', (int) $type);
		}

		return $this->fetchAll($select);
	}

	/**
	 * Save rows to the database. (insert or update)
	 *
	 * @param array $values
	 * @return	boolean
	 * @todo add transactions!
	 */
	public function saveRows($values)
	{
		$conferenceId = (int) $values['conference_id'];

		$db = $this->getAdapter();
		$metadata = $this->info('metadata');

		$currentValues = $db->fetchCol(
			"SELECT timeslot_id from timeslots where conference_id=".$conferenceId
		);

		$modifiedValues = array();

		foreach ($values['dynamic'] as $key => $timeslot) {

			$timeslotId = (int) substr(strstr($key, '_'), 1);
			$timeslot['conference_id'] = $conferenceId;

			// modify datetime values to SQL timestamps
			foreach ($timeslot as $column => $val) {
				if ($metadata[$column]['DATA_TYPE'] == 'timestamptz') {
					$zd = new Zend_Date($val, 'dd/MM/yyyy hh:mm');
					$timeslot[$column] = $zd->get(Zend_Date::ISO_8601);
				}
			}

			if (in_array($timeslotId, $currentValues)) {
				// do update
				$this->update($timeslot, 'timeslot_id = '.$timeslotId);
				$modifiedValues[] = $timeslotId;
			} else {
				// do insert
				$modifiedValues[] = $this->insert($timeslot);
			}

		}

		// delete records that are left
		$diff = array_diff($currentValues, $modifiedValues);
		if (!empty($diff)) {
			$this->delete('timeslot_id in ('.implode(',', $diff).')');
		}

		return true;
	}
}
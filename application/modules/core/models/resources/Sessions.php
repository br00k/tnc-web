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
	 * @revision   $Id: Sessions.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
	 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Sessions extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'sessions';

	protected $_primary = 'session_id';

	protected $_rowClass = 'Core_Resource_Session_Item';

	protected $_rowsetClass = 'Core_Resource_Session_Set';

	/**
	 * Attach your observers here
	 *
	 */
	public function init()
	{
		$config = Zend_Registry::get('conference');

		if (isset($config['gcal_url']) &&
			isset($config['gcal_username']) &&
			isset($config['gcal_password'])) {
				$this->attachObserver(new Core_Model_Observer_Sessiongcal());
		}

		$this->attachObserver(new Core_Model_Observer_Sessionsubscriber());
	}

	/**
	 * Gets session by primary key
	 *
	 * @return object Core_Resource_Session_Item
	 */
	public function getSessionById($id)
	{
		return $this->find((int) $id)->current();
	}

	/**
	 * Get sessions for use in form select
	 *
	 * @param	string	$empty	Empty string to prepend to array
	 * @return	array
	 */
	public function getSessionsForSelect($conferenceId = null, $empty = null)
	{
		$return = $this->getAdapter()->fetchPairs($this->select()
			->from('sessions', array('session_id', 'title'))
			->where('conference_id = ?', $this->getConferenceId())
			->order('lower(title) ASC')
		);

		if ($empty) {
			$return[0] = $empty;
			asort($return);
		}

		return $return;
	}

	public function getSessionByTimeAndLocation($time, $location)
	{
		return $this->fetchRow($this->select()
			->where('timeslot_id = ?', $time)
			->where('location_id = ?', $location)
		);
	}

	/**
	 * Get all sessions data
	 *
	 * @param	array	$sessions	Array of session_id's
	 * @return	object	Core_Resource_Session_Set
	 */
	public function getAllSessions($sessions)
	{
		$select = $this->select()
					   ->where('conference_id = ?', $this->getConferenceId());
		if ($sessions) {
			$select->where('session_id IN (?)', $sessions);
		}

		return $this->fetchAll($select);
	}

	/**
	 *
	 *
	 */
	public function getSessions($paged = null, $order = array())
	{
		$grid = array();
		$grid['cols'] = $this->getGridColumns();
		$grid['primary'] = $this->_primary;

		$select = $this->select();

		if (!empty($order[0])) {
			if ($order[0] == 'updated') {
				$order = $order[0].' '.$order[1];
			} else {
				$order = 'lower('.$order[0].') '.$order[1];
			}
		} else {
			$order = 'lower(title) ASC';
		}
		$select->order($order);

		$select->from('sessions', array_keys($this->getGridColumns()))
			   ->where('conference_id = ?', $this->getConferenceId());

		if ($paged) {

			$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);

			$paginator = new Zend_Paginator($adapter);
			$paginator->setCurrentPageNumber((int) $paged)
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
	 */
	private function getGridColumns()
	{
		return array(
			// session_id is hidden so I don't have to provide a label
			'session_id' => array('field' => 'session_id', 'sortable' => true, 'hidden' => true),
			'title' => array('field' => 'title', 'label' => 'Title', 'sortable' => true),
			'gcal_event_id' => array('field' => 'gcal_event_id', 'label' => 'Google', 'sortable' => false)
		);

	}

}
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
 * Session rowset
 *
 * @package Core_Resource
 * @subpackage Core_Resource_Session
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Session_Set extends Zend_Db_Table_Rowset_Abstract
{

	/**
	 * Group all sessions by day
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
				case 'location':
					$list[$row->location_abbreviation][] = $row;
				break;
			}
		}
		// sort by array key
		ksort($list);
		return $list;
	}
	
	/**
	 * Get all chairs
	 *
	 * @return array
	 */
	public function getChairs()
	{
		$query = "select u.fname, u.lname, u.email, su.session_id from sessions_users su
		left join users u on (su.user_id = u.user_id)";

		return $this->getTable()->getAdapter()->query($query)->fetchAll();
	}

	public function getRowBySessionId($sessionId)
	{
		foreach ($this as $row) {
			if ($row->session_id == $sessionId) {
				return $row;
			}
		}
	}

	/**
	 * Get presentation count grouped by sessions
	 *
	 * @return array
	 */
	public function getPresentationCount()
	{
		$query = "select session_id, count(presentation_id) from sessions_presentations group by session_id";
		return $this->getTable()->getAdapter()->fetchPairs($query);
	}

	/**
	 * Helper method to get session_id array
	 *
	 * @return array
	 */
	private function _getSessionIds()
	{
		$ids = array();
		foreach ($this as $row) {
			$ids[] = $row->session_id;
		}
		return $ids;
	}

	/**
	 * Get all presentations grouped by session_id/presentation_id
	 *
	 * @return array
	 */
	public function getAllPresentations()
	{
		$list = array();

		$sessionIds = $this->_getSessionIds();

		$query = "select * from sessions_presentations sp
		left join presentations p on (sp.presentation_id = p.presentation_id)
		where sp.session_id IN (".implode(',',$sessionIds).") order by displayorder asc";

		$presentations = $this->getTable()->getAdapter()->fetchAll($query);

		foreach ($presentations as $presentation) {
			$list[$presentation['session_id']][$presentation['presentation_id']] = $presentation;
		}

		return $list;
	}

	/**
	 * Get all speakers grouped by session_id/user_id
	 *
	 * @param	boolean		$unique		Set to true if you want to retrieve unique speakers
	 *									because multiple presentations can have the same speaker
	 * @return array
	 */
	public function getAllSpeakers($unique = false)
	{
		$list = array();
		$method = ($unique) ? 'fetchAssoc' : 'fetchAll';

		$sessionIds = $this->_getSessionIds();
		
		if (count($sessionIds) == 0) {
			return false;
		}

		$query = "select user_id, session_id, presentation_id, fname, lname, email, organisation
		 from vw_sessions_speakers where session_id IN (".implode(',',$sessionIds).")";

		$speakers = $this->getTable()->getAdapter()->$method($query);

		foreach ($speakers as $speaker) {
			$list[$speaker['session_id']][$speaker['user_id']] = $speaker;
		}

		return $list;
	}
}
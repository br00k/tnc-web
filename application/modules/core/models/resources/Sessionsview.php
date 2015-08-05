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
 * @revision   $Id: Sessionsview.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Sessionsview extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'vw_sessions';

	protected $_primary = 'session_id';

	protected $_rowClass = 'Core_Resource_Session_Item';

	protected $_rowsetClass = 'Core_Resource_Session_Set';

	public function init() {}

	/**
	 * Gets session by primary key
	 * @return object Core_Resource_Session_Item
	 */
	public function getSessionById($id)
	{
		return $this->find( (int)$id )->current();
	}

	/**
	 * Get sessions by datetime
	 *
	 * @param	Zend_Date		$date
	 * @return	Core_Resource_Session_Set
	 */
	public function getSessionsByDate(Zend_Date $date = null)
	{
		$date = new Zend_Date($date);
		$zd = $date->get(Zend_Date::ISO_8601);

		return $this->fetchAll(
		    $this->select()
		    	 ->where("tstart <= '".$zd."'")
		    	 ->where("tend >= '".$zd."'")
		);
	}

	/**
	 * Get sessions after datetime but only on $date day
	 *
	 * @param	Zend_Date	$date
	 * @return	Core_Resource_Session_Set
	 */
	public function getSessionsAfterDate(Zend_Date $date = null)
	{
		$date = new Zend_Date($date);
		$zd = $date->get(Zend_Date::ISO_8601);

		return $this->fetchAll(
			$this->select()
			    ->where("tstart >= '".$zd."'")
			    ->where("date_trunc('day', tstart) = (select DATE '".$zd."')")
			    ->order('tstart')
			);
	}

	/**
	 * Get sessions before datetime
	 *
	 * @param	Zend_Date	$date
	 * @return	Core_Resource_Session_Set
	 */
	public function getSessionsBeforeDate(Zend_Date $date = null)
	{
		$date = new Zend_Date($date);
		$zd = $date->get(Zend_Date::ISO_8601);

		return $this->fetchAll(
			$this->select()
			    ->where("tend <= '".$zd."'")
			    ->order('tstart')
			    ->order('location_abbreviation')
			);
	}

	/**
	 * Get sessions by session_id's
	 *
	 * @param	$submissions
	 */
	public function getSessionsByIds(array $submissions)
	{
		$sessionIds = array();

		foreach ($submissions as $submission) {
			if ($submission['session_id']) {
				$sessionIds[] = (int) $submission['session_id'];
			}
		}
		$sessionIds = implode(',', $sessionIds);

		if (!empty($sessionIds)) {
			return $this->fetchAll(
				$this->select()
					 ->where('session_id IN ('.$sessionIds.')')
			);
		}
		return false;

	}

	/**
	 * Get all session information
	 *
	 * @return	Core_Resource_Session_Set
	 */
	public function getAllSessionData()
	{
		return $this->fetchAll(
			$this->select()
				 ->where('conference_id=?', $this->getConferenceId())
		);
	}

}
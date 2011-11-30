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

/**
 * Session row
 *
 * @package Core_Resource
 * @subpackage Core_Resource_Session
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Session_Item extends TA_Model_Resource_Db_Table_Row_Abstract implements TA_Form_Element_User_Interface
{

	protected $_manyToManyIds;

	/**
	 * Required by TA_Form_Element_User
	 *
	 */
	public function getUsers($allData = null)
	{
		$this->_manyToManyIds = $userIds = $this->getTable()->getAdapter()->fetchPairs(
			"select session_user_id, user_id from sessions_users where session_id=:session_id",
			array(':session_id' => $this->session_id)
		);

		$userModel = new Core_Model_User();
		$filter = new stdClass();
		$filter->user_id = $userIds;
		if ($userIds) {
			$users = $userModel->getUsers(null, null, $filter);
			return $users['rows'];
		}
		return false;
	}

	/**
	 * Get primary key values of many to many join table
	 * in this case sessions_users
	 */
	public function getManyToManyIds()
	{
		return array_flip($this->_manyToManyIds);
	}

	/**
	 * Get chairs belonging to this session
	 *
	 * @param boolean $allData Get all data or just what is needed for a select
	 * @return array
	 */
	public function getChairs($allData = null)
	{
		if ($allData) {
			$query = "select u.*, su.session_user_id as id from sessions_users su
			left join users u on (su.user_id = u.user_id)
			where su.session_id=:session_id";
		} else {
			$query = "select u.email, su.session_user_id as id from sessions_users su
			left join users u on (su.user_id = u.user_id)
			where su.session_id=:session_id";
		}

		return $this->getTable()->getAdapter()->query(
			$query, array(':session_id' => $this->session_id)
		)->fetchAll();
	}

	/**
	 * Is this session in progress?
	 *
	 * @return mixed	Zend_Date on success, false on failure
	 */
	public function isNow()
	{
		//$date = new Zend_Date(array(
		//  'year' => 2011,
		//  'month' => 5,
		//  'day' => 16,
		//  'hour' => 14,
		//  'minute' => 10,
		//  'second' => 10));

		$date = new Zend_Date();

		if ( ( $date->isLater($this->tstart, Zend_Date::ISO_8601)  ) &&
		( $date->isEarlier($this->tend, Zend_Date::ISO_8601) )  ) {
		    return $date;
		}
		return false;
	}

	/**
	 * Has this session ended?
	 *
	 * @return mixed	Zend_Date on success, false on failure
	 */
	public function hasEnded()
	{
		//$date = new Zend_Date(array(
		//  'year' => 2011,
		//  'month' => 5,
		//  'day' => 18,
		//  'hour' => 14,
		//  'minute' => 10,
		//  'second' => 10));

		$date = new Zend_Date();

		if ( $date->isLater($this->tend, Zend_Date::ISO_8601) )  {
		    return $date;
		}
		return false;
	}

	/**
	 * Get presentations belonging to this session
	 * Note the order by displayorder, this is very important for ordering!!!
	 *
	 * @return array
	 */
	public function getPresentations()
	{
		$query = "select * from sessions_presentations sp
		left join presentations p on (sp.presentation_id = p.presentation_id)
		where sp.session_id=:session_id order by displayorder asc";

		return $this->getTable()->getAdapter()->query(
			$query, array(':session_id' => $this->session_id)
		)->fetchAll();
	}

	public function getEvaluation()
	{
		$query = "select * from session_evaluation where session_id=:session_id";

		return $this->getTable()->getAdapter()->query(
			$query, array(':session_id' => $this->session_id)
		)->fetchAll();
	}

	public function getFiles()
	{
		return $this->getTable()->getAdapter()->fetchAll(
			"select * from vw_session_files where session_id=:session_id",
			array(':session_id' => $this->session_id)
		);
	}

	/**
	 * Get speakers belonging to this session
	 *
	 * @param	boolean		$unique		Set to true if you want to retrieve unique speakers
	 *									because multiple presentations can have the same speaker
	 *
	 * @return array
	 * @todo this can be replaced by a query to vw_sessions_speakers
	 */
	public function getSpeakers($unique = false)
	{
		$method = ($unique) ? 'fetchAssoc' : 'fetchAll';

		// note: user_id must be first in the list for fetchAssoc to properly work.
		$query = "select u.user_id, pu.presentation_id, u.fname, u.lname, u.organisation, u.email from presentations_users pu
		left join users u on (pu.user_id = u.user_id) where pu.presentation_id in (
		select presentation_id from vw_session_presentations sp where sp.session_id=:session_id order by u.lname
		)";

		return $this->getTable()->getAdapter()->$method(
			$query, array(':session_id' => $this->session_id)
		);
	}

	/**
	 * @todo this can replace the getSpeakers() method!
	 *
	 * @param $grouped	Group by presentation_id
	 */
	public function getGroupedSpeakers($unique = false, $grouped = false)
	{
		$list = array();

		$method = ($unique) ? 'fetchAssoc' : 'fetchAll';

		$query = "select user_id, presentation_id, fname, lname, organisation, email
		from vw_sessions_speakers where session_id=:session_id order by lname";

		$speakers = $this->getTable()->getAdapter()->$method(
			$query, array(':session_id' => $this->session_id)
		);

		if ($grouped) {
			foreach ($speakers as $speaker) {
				$list[$speaker['presentation_id']][$speaker['user_id']] = $speaker;
			}

			return $list;
		}
		return $speakers;
	}

}
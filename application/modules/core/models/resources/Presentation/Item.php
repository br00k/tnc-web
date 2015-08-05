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
 * @revision   $Id: Item.php 41 2011-11-30 11:06:22Z gijtenbeek@terena.org $
 */

/**
 * Presentation row
 *
 * @package Core_Resource
 * @subpackage Core_Resource_Presentation
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Presentation_Item extends TA_Model_Resource_Db_Table_Row_Abstract implements TA_Form_Element_User_Interface
{

	protected $_manyToManyIds;

	/**
	 * Required by TA_Form_Element_User
	 *
	 * @return	Zend_Db_Table_Rowset
	 */
	public function getUsers()
	{
		$this->_manyToManyIds = $userIds = $this->getTable()->getAdapter()->fetchPairs(
			"select presentation_user_id, user_id from presentations_users where presentation_id=:presentation_id",
			array(':presentation_id' => $this->presentation_id)
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
	 * in this case presentations_users
	 */
	public function getManyToManyIds()
	{
		return array_flip($this->_manyToManyIds);
	}

	public function getSpeakers()
	{
		return $this->getTable()->getAdapter()->fetchAll(
			"select * from vw_speakers where presentation_id=:presentation_id",
			array(':presentation_id' => $this->presentation_id)
		);
	}

	public function getFiles()
	{
		return $this->getTable()->getAdapter()->fetchAll(
			"select * from vw_presentation_files where presentation_id=:presentation_id",
			array(':presentation_id' => $this->presentation_id)
		);
	}

	public function getSession()
	{
		return $this->getTable()->getAdapter()->fetchRow(
			"select * from vw_session_presentations where presentation_id=:presentation_id",
			array(':presentation_id' => $this->presentation_id)
		);
	}

	/**
	 * Is this current time before the edit deadline?
	 *
	 * Assumes config directive core.presentation.deadline
	 *
	 * @return boolean
	 */
	public function isBeforeEditDeadline()
	{
		$config = Zend_Registry::get('config');

		$tStart = $this->getTable()->getAdapter()->fetchOne(
			"select tstart from vw_sessions left join vw_session_presentations sp"
			." ON (vw_sessions.session_id = sp.session_id) where presentation_id=:presentation_id",
			array(':presentation_id' => $this->presentation_id)
		);

		if ($tStart) {
			$now = new Zend_Date();
			$tStart = new Zend_Date($tStart, Zend_Date::ISO_8601);
			$deadline = $tStart->sub($config->core->presentation->deadline, Zend_Date::SECOND);

			if ( $now->isEarlier($deadline) ) {
			   return true;
			}
		}

		return false;
	}
}
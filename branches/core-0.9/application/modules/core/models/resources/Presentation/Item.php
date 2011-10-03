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
 * @revision   $Id: Item.php 598 2011-09-15 20:55:32Z visser $
 */
/**
	* This class represents a single record
	* Methods in this class could include logic for a single record
	* for example sticking first_name and last_name together in a getName() method
	* @todo: move all these methods to their respective resources and call those? 
	*/
class Core_Resource_Presentation_Item extends TA_Model_Resource_Db_Table_Row_Abstract
{

	public function getUsers()
	{
		$query = "select u.email, u.organisation, pu.presentation_user_id as id from presentations_users pu
		left join users u on (pu.user_id = u.user_id)
		where pu.presentation_id=:presentation_id";

		return $this->getTable()->getAdapter()->query(
			$query, array(':presentation_id' => $this->presentation_id)
		)->fetchAll();
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
}
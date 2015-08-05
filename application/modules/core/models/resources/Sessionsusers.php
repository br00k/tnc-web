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
 * @revision   $Id: Sessionsusers.php 37 2011-10-18 14:09:56Z gijtenbeek@terena.org $
 */

/**
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Sessionsusers extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'sessions_users';

	protected $_primary = 'session_user_id';

	public function init() {}

	/**
	 * Gets item by primary key
	 *
	 * @param	integer		$id
	 * @return	Zend_Db_Table_Row
	 */
	public function getItemById($id)
	{
		return $this->find( (int)$id )->current();
	}

	/**
	 * returns item based on id values
	 *
	 * @param	array	$data	Session_id and User_id values
	 * @return	object	Zend_Db_Table_Row
	 */
	public function getItemByValues(array $data)
	{
		return $this->fetchRow(
					$this->select()
					->where('session_id = ?', $data['session_id'])
					->where('user_id = ?', $data['user_id'])
				);
	}

	/**
	 * Gets item by user id
	 *
	 * @param	integer		$id 	user id value
	 * @return	Zend_Db_Table_Row
	 */
	public function getItemByUserId($id)
	{
		return $this->fetchRow(
					$this->select()
					->where('user_id = ?', $id)
				);
	}

}
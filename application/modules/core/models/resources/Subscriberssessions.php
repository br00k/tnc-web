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
 * @revision   $Id: Subscriberssessions.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Subscriberssessions extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'subscribers_sessions';

	protected $_primary = 'subscriber_session_id';

	public function init() {}

	/**
	 * Gets item by primary key
	 * @return object Zend_Db_Table_Row
	 */
	public function getItemById($id)
	{
		return $this->find( (int)$id )->current();
	}

	/**
	 * returns item based on id values
	 * This is a particular action for many to many tables
	 *
	 * @param	array	$data	session_id and user_id values
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
	 * Get sessions user is subscribed to or
	 * users who are subscribed to a specific session
	 *
	 * @param	integer		$userId	user_id
	 * @param	integer		$sessionId	session_id
	 * @return	mixed		array of session_id's
	 */
	public function getSubscriptions($userId = null, $sessionId = null)
	{
		// no user_id provided, get one from the auth service
		if (!isset($user_id)) {
			if ($auth = Zend_Auth::getInstance()->getIdentity() ) {
				$userId = ($userId) ? $userId : $auth->user_id;
			} else {
				return false;
			}
		}

		// no session id provided, get all sessions user is subscribed to
		if (!isset($sessionId)) {
			return $this->getAdapter()->fetchCol(
				$this->select()
				->from($this->_name, 'session_id')
				->where('user_id = ?', $userId)
			);
		}

		// session_id provided, get all users who are subscribed to specific session
		return $this->getAdapter()->fetchAll(
			"select u.fname, u.lname, u.email from subscribers_sessions ss left join users u on (u.user_id = ss.user_id)
			where ss.session_id=".$sessionId
		);
	}

}
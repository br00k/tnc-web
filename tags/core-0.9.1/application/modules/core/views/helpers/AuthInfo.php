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
 * @revision   $Id: AuthInfo.php 598 2011-09-15 20:55:32Z visser $
 */

/**
 * AuthInfo View helper
 *
 * @package Core_View_Helper
 */
class Core_View_Helper_AuthInfo extends Zend_View_Helper_Abstract
{

	protected $_authService;

	public function authInfo($info = null)
	{
		if (!$this->_authService) {
			$this->_authService = new Core_Service_Authentication();
		}

		if ($info) {
			return $this->_authService->getAuth()->getIdentity()->$info;
		} else {
			return $this;
		}

	}

	/**
	 * Get full name of signed in user
	 *
	 * @return string Full name
	 */
	public function getFullName()
	{
		if ($this->isLoggedIn()) {
			return $this->_authService->getAuth()->getIdentity()->getFullName();
		}
	}

	/**
	 * Is client signed in?
	 *
	 * @return boolean
	 */
	public function isLoggedIn()
	{
		return $this->_authService->getAuth()->hasIdentity();
	}

	/**
	 * Convenience method to check if signed in user is an admin
	 * assumes that role key is an array
	 *
	 * @return boolean
	 */
	public function isAdmin()
	{
		if ($this->isLoggedIn()) {
			return in_array('admin', $this->authInfo('role'), true);
		}
		return false;
	}
	
	/**
	 * Convenience method to check if user has certain role,
	 * returns true if user is an admin
	 *
	 * @param	string		$role	Role name
	 * @return 	boolean
	 */
	public function hasRole($role)
	{
		if (!$this->isLoggedIn()) {
			return false;
		}
		if ($this->isAdmin()) {
			return true;
		}
		return in_array($role, $this->authInfo('role'), true);
	}
	
	/**
	 * Get sessions user is chair off
	 *
	 * @return array
	 */
	 public function getSessionsToChair()
	 {
		if (!$this->isLoggedIn()) {
			return array();
		}
		return $this->_authService->getAuth()->getIdentity()->getSessionsToChair();
	 }
	 
	/**
	 * Get presentations from user
	 *
	 * @return array
	 */
	public function getMyPresentations()
	{	
		if (!$this->isLoggedIn()) {
			return array();
		}
		return $this->_authService->getAuth()->getIdentity()->getMyPresentations();
	}

}
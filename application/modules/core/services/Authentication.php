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
	 * @revision   $Id: Authentication.php 41 2011-11-30 11:06:22Z gijtenbeek@terena.org $
	 */

/**
 * Authentication Service
 *
 * @package		Core_Service
 * @author Christian Gijtenbeek
 */
class Core_Service_Authentication {

	/**
	 * @var Core_Model_User
	 */
	protected $_userModel;

	/**
	 * @var Zend_Auth
	 */
	protected $_auth;

	/**
	 * Defines the type of authentication to use
	 * @var string
	 */
	protected $_type;

	/**
	 * Invite hash
	 * @var string
	 */
	protected $_invite;

	/**
	 * Redirect url
	 * @var string
	 */
	protected $_returnTo;

	/**
	 * Constructor, loads user model
	 *
	 * @param	string	$invite		Invite hash
	 * @return void
	 */
	public function __construct($invite = null)
	{
		$this->_userModel = new Core_Model_User();
		$this->_invite = $invite;
	}

	/**
	 * Perform authentication
	 *
	 * @param	array	$values
	 * @return	mixed	true on success, else error message
	 */
	public function authenticate($values)
	{
		$adapter = $this->_getAuthAdapter($values);

		$auth = $this->getAuth();

		$result = $auth->authenticate($adapter);

		if ($result->isValid()) {
			// persistent storage
			$storage = $auth->getStorage();

			// set custom user attributes
			if (!$user = $this->_userModel->getUserBySmartId($result->getIdentity())) {
				if ($this->_invite) {
					// update user
					$user = $this->_userModel->saveUserFromFederatedIdentity(
						$result->getIdentityAttributes(),
						$this->_invite
					);
				} else {
					// insert user
					$user = $this->_userModel->saveUserFromFederatedIdentity($result->getIdentityAttributes());
				}
			}

			$user->updateAttributes();

			$storage->write($user);

			return true;
		} else {
			return $result->getMessages();
		}

	}

	/**
	 * Gets Zend_Auth adapter
	 *
	 * @return Zend_Auth
	 */
	public function getAuth()
	{
		if (!$this->_auth) {
			return Zend_Auth::getInstance();
		}
	}

	/**
	 * Get authentication type
	 *
	 * @return string
	 */
	protected function _getAuthType()
	{
		return $this->_type;
	}

	/**
	 * Get different authentication adapaters based on parameter
	 *
	 * @param	array	$values
	 * @return	Zend_Auth_Adapter_Interface
	 */
	protected function _getAuthAdapter(array $values)
	{
		if (isset($values['authsource'])) {
			$this->_type = 'federated';
			return $this->_getAuthAdapterFederated($values);
		}
		if (isset($values['password'])) {
			$this->_type = 'advanced';
			return $this->_getAuthAdapterAdvanced($values);
		} elseif (isset($values['organisation'])) {
			$this->_type = 'basic';
			return $this->_getAuthAdapterBasic($values);
		}
		throw new Exception('No valid authentication adapter found');
	}

	protected function _getAuthAdapterFederated($values)
	{
		return new TA_Auth_Adapter_Federated($values['authsource']);
	}

	protected function _getAuthAdapterBasic($values)
	{
		$adapter = new Zend_Auth_Adapter_DbTable(
			Zend_Db_Table_Abstract::getDefaultAdapter(),
			'users',
			'email',
			'organisation'
		);

		$adapter->setIdentity($values['email'])
				->setCredential($values['organisation'])
				->setCredentialTreatment("lower(?)");

		// only select active users
		$select = $adapter->getDbSelect();
		$select->where('active = true');
		// only select users who can *not* login with password. They would need the advanced adapater
		$select->where('password IS NULL');

		return $adapter;
	}

	protected function _getAuthAdapterAdvanced($values)
	{
		$adapter = new Zend_Auth_Adapter_DbTable(
			Zend_Db_Table_Abstract::getDefaultAdapter(),
			'users',
			'email',
			'password'
		);

		// salt with static *and* dynamic salt. Dynamic salt is stored in database and static salt in config
		$adapter->setIdentity($values['email'])
				->setCredential($values['password'])
				// concatentate strings in the right order
				->setCredentialTreatment("md5('".Zend_Registry::get('config')->_staticSalt."' || ? || password_salt)");

		// only select active users
		$select = $adapter->getDbSelect();
		$select->where('active = true');

		return $adapter;
	}

}

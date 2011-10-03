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
 * @revision   $Id: Abstract.php 598 2011-09-15 20:55:32Z visser $
 */

/**
 * Abstract base model with ACL functionality.
 *
 * @usage To turn a model into an ACL resource extend from this class.
 * If you do not want ACL functionality extend from TA_Model_Abstract instead.
 *
 */

class TA_Model_Acl_Abstract extends TA_Model_Abstract implements Zend_Acl_Resource_Interface {

	/**
	 * @var Zend_Acl
	 */
	protected $_acl;

	/**
	 * @var string
	 */
	protected $_identity;

	/**
	 * Returns the string identifier of the Resource
	 * You can always overload this method if needed. If you do, make sure the resource Id is unique
	 *
	 * @return	string	Name of the entity being modelled, eg: 'User', 'Group'
	 */
	public function getResourceId()
	{
		return substr(strrchr(get_class($this), "_"), 1);
	}


	/**
	 * This methods allows for injecting an ACL object.
	 * If no ACL is given it lazyloads the ACL.
	 *
	 */
    public function setAcl(Core_Model_Acl_Core $acl)
    {
        $this->_acl = $acl;
        Zend_Registry::set('acl', $acl);

        return $this;
    }

	/**
	 * Getter for ACL object.
	 *
	 * @return	Core_Model_Acl_Core ACL object
	 */
    public function getAcl()
    {
        if (false === Zend_Registry::isRegistered('acl')) {
            $this->setAcl(new Core_Model_Acl_Core());
        }
        return Zend_Registry::get('acl');
    }

	/**
     * Set the identity of the current request
     *
     * @param	array|string|null|Zend_Acl_Role_Interface $identity
     * @return	TA_Model_Abstract
     */
    public function setIdentity($identity)
    {
        if (is_array($identity)) {
            if (!isset($identity['role'])) {
                $identity['role'] = 'guest';
            }
            $identity = new Zend_Acl_Role($identity['role']);
        } elseif (is_scalar($identity) && !is_bool($identity)) {
            $identity = new Zend_Acl_Role($identity);
        } elseif (null === $identity) {
            $identity = new Zend_Acl_Role('guest');
        } elseif (!$identity instanceof Zend_Acl_Role_Interface) {
            throw new TA_Model_Exception('Invalid identity provided');
        }
        $this->_identity = $identity;
        return $this;
    }

    /**
     * Get the identity of the current request, defaults to guest
     *
     * @return string
     */
    public function getIdentity()
    {
        if (null === $this->_identity) {
            $auth = Zend_Auth::getInstance();
            if (!$auth->hasIdentity()) {
                return 'guest';
            }
            $this->setIdentity($auth->getIdentity());
        }

        return $this->_identity;
    }

	/**
	 * Helper method to check a user against the ACL
	 *
	 * @param	string	$action		Action to check
	 * @return	boolean
	 */
    public function checkAcl($action)
    {
        return $this->getAcl()->isAllowed(
            $this->getIdentity(),
            $this,
            $action
        );
    }

}
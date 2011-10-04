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
 * @revision   $Id: AclCheck.php 598 2011-09-15 20:55:32Z visser $
 */

/**
 * Acl View helper
 *
 * @package Core_View_Helper
 */
class Core_View_Helper_AclCheck extends Zend_View_Helper_Abstract
{

	protected $_acl;

	protected $_auth;

	public function aclCheck($controller = null, $action = null)
	{
		if (!$this->_acl) {
			$this->_acl = Zend_Registry::get('acl');
    		$this->_auth = Zend_Auth::getInstance();
		}

		if (!$controller) {
			$request = Zend_Controller_Front::getInstance()->getRequest();
			$controller = $request->getControllerName();
			$action = $request->getActionName();
		}

        if (!$this->_auth->hasIdentity()) {
       		$role = 'guest';
        } else {
        	//$role = $auth->getIdentity()->role;
        	$role = $this->_auth->getIdentity();
        }

    	// check if ACL resource exists
    	if (!$this->_acl->has(ucfirst($controller))) {
		   return false;
		}

		// check default request
		if (!$this->_acl->isAllowed($role, ucfirst($controller), $action)) {
			return false;
		}

		return true;
	}

}
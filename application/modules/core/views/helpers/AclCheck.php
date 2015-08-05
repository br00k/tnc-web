<?php

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
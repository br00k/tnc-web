<?php
/**
 * Make sure session chairs can only change sessions assigned to them
 *
 */
class Core_Model_Acl_UserCanUpdateSessionAssertion implements Zend_Acl_Assert_Interface
{
    /**
     * This assertion should receive the actual User objects.
     *
     * @param Zend_Acl $acl
     * @param Zend_Acl_Role_Interface $user
     * @param Zend_Acl_Resource_Interface $model
     * @param $privilege
     * @return bool
     */
    public function assert(Zend_Acl $acl, Zend_Acl_Role_Interface $user = null, Zend_Acl_Resource_Interface $model = null, $privilege = null)
    {
		if ($user) {
			$sessions = $user->getSessionsToChair();
		} else {
			return false;
		}

		$request = Zend_Controller_Front::getInstance()->getRequest();
		$param = ( $request->getParam('id') ) ? $request->getParam('id') : $request->getParam('session_id');

		// perform check
		if ($param !== null && in_array( (int) $param, $sessions, true)) {
			return true;
		} else {
			return false;
		}


    }
}
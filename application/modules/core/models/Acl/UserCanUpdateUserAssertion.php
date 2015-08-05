<?php
/**
 * Make sure users can only change their own information
 *
 */
class Core_Model_Acl_UserCanUpdateUserAssertion implements Zend_Acl_Assert_Interface
{
    /**
     * This assertion should receive the actual User objects.
     *
     * @param Zend_Acl $acl
     * @param Zend_Acl_Role_Interface $user
     * @param Zend_Acl_Resource_Interface $userModel
     * @param $privilege
     * @return bool
     */
    public function assert(Zend_Acl $acl, Zend_Acl_Role_Interface $user = null, Zend_Acl_Resource_Interface $userModel = null, $privilege = null)
    {
		$userId = null;
		$auth = Zend_Auth::getInstance();

		if ($auth->hasIdentity()) {
			$userId = $auth->getStorage()->read()->user_id;
		} else {
			return false;
		}
		
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$param = (int) ( $request->getParam('id') ) ? (int) $request->getParam('id') : (int) $request->getParam('user_id');

		// check to ensure that user is only modifying their own information
		if ($param !== null && $userId === $param) {
			return true;
		} else {
			return false;
		}


    }
}
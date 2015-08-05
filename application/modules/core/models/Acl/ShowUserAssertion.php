<?php
/**
 * Make sure users can only show presenter user information
 *
 */
class Core_Model_Acl_ShowUserAssertion implements Zend_Acl_Assert_Interface
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
    	$request = Zend_Controller_Front::getInstance()->getRequest();

    	// have to hack this because of the 'oneitem' route all controller/action combo's will execute this assertion
    	// @todo: change this in the oneitem route somehow?
    	if ($request->getControllerName() !== 'user') {
    		return true;
    	}
		$param = $request->getParam('id', null);

		if (!$param) return true;

		// perform check
		if ( $model->getUserById($param)->hasRole(array('presenter', 'chair')) ) {
			return true;
		} else {
			return false;
		}

    }
}
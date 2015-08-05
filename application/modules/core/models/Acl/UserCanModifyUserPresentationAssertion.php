<?php
/**
 * Make sure users can only modify presentation user links of their own presentations 
 *
 */
class Core_Model_Acl_UserCanModifyUserPresentationAssertion implements Zend_Acl_Assert_Interface
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
			$presentations = $user->getMyPresentations();
		} else {
			return false;
		}

		$param = Zend_Controller_Front::getInstance()
			->getRequest()
			->getParam('id', null);

		if (!$param) return false;

		// get presentation_id
		$presentation = $model->getResource('presentationsusers')->getItemById($param)->presentation_id;

		// perform check
		if ($presentation !== null && in_array( (int) $presentation, $presentations, true)) {
			return true;
		} else {
			return false;
		}

    }
}
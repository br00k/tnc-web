<?php
/**
 * Make sure feedback is only provided for own feedback id
 *
 */
class Core_Model_Acl_GuestCanSaveFeedbackAssertion implements Zend_Acl_Assert_Interface
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
		$feedbackId = $model->getFeedbackId();

		//$request = Zend_Controller_Front::getInstance()->getRequest();
		//$param = $request->getParam('id');
		$post = $model->getPostArray();
		// posted feedback id
		$param = $post['id'];

		// check if posted feedbackId is the same as feedbackId retrieved from cookie 
		if ($param !== null && $param == $feedbackId ) {
			return true;
		} else {
			return false;
		}


    }
}
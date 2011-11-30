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
 * @revision   $Id$
 */

/**
 * Make sure session chairs can only change sessions assigned to them
 *
 * @package Core_Model
 * @subpackage Core_Model_Acl
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
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
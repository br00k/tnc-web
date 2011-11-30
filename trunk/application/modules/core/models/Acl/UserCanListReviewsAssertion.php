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
 * @revision   $Id: UserCanUpdatePresentationAssertion.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */

/**
 * Reviewers can only list reviews after certain date
 * This date is configurable in the conference menu
 *
 * @package Core_Model
 * @subpackage Core_Model_Acl
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Model_Acl_UserCanListReviewsAssertion implements Zend_Acl_Assert_Interface
{
    /**
     * This assertion should receive the actual Submission objects.
     *
     * @param Zend_Acl $acl
     * @param Zend_Acl_Role_Interface $user
     * @param Zend_Acl_Resource_Interface $model
     * @param $privilege
     * @return bool
     */
    public function assert(Zend_Acl $acl, Zend_Acl_Role_Interface $user = null, Zend_Acl_Resource_Interface $model = null, $privilege = null)
    {
		$conference = Zend_Registry::get('conference');

		if (!isset($conference['review_visible'])) {
			return true;
		}

		$now = new Zend_Date();
		
		// perform check
		if ( $now->isLater($conference['review_visible']) ) {
			return true;
		}
		return false;
    }
}
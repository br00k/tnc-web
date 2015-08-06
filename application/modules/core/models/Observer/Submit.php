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
	 * @revision   $Id: Submit.php 25 2011-10-04 20:46:05Z visser@terena.org $
	 */

/**
 * Submit observer. 
 *
 * Following methods can be implemented: _postUpdate, _postInsert, _postDelete
 * @package Core_Model
 * @subpackage Core_Model_Observer
 */
class Core_Model_Observer_Submit extends TA_Model_Acl_Abstract implements TA_Model_Observer_Interface
{

	public function _postInsert(TA_Model_Observed_Interface $subject, $msg)
	{
		$conference = Zend_Registry::get('conference');

		$userModel = new Core_Model_User();
		$values['submit'] = $subject->toArray();

		$mailer = new TA_Controller_Action_Helper_SendEmail();
		$mailer->sendEmail(array(
		   'to_email' => $conference['email'],
		   'html' => true,
		   'subject' => 'CORE Observer: '.__CLASS__,
		   'template' => 'submit/postinsert'
		), $values);
	}
}
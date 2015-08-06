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
 * @revision   $Id: Core.php 104 2013-04-08 11:58:49Z gijtenbeek@terena.org $
 */

/**
 * Defines ACL rules
 *
 * @package Core_Model
 * @subpackage Core_Model_Acl
 * @see http://weierophinney.net/matthew/archives/201-Applying-ACLs-to-Models.html
 */
class Core_Model_Acl_Core extends Zend_Acl {

	/**
	 * Define resources and add privileges. For generic use, define resources and privileges here.
	 * You can also chose to define it in the model itself or in setAcl() from TA_Model_Acl_Abstract
	 */
	public function __construct()
	{
		$log = Zend_Registry::get('log');
		$log->info(__METHOD__);
		// Define Roles
		$this->addRole(new Core_Model_Acl_Role_Guest) // not authenicated
			 ->addRole(new Core_Model_Acl_Role_User, 'guest') // user role is automatically assigned to logged in users
			 ->addRole(new Core_Model_Acl_Role_Submitter, 'user')
			 ->addRole(new Core_Model_Acl_Role_Presenter, 'user')
			 ->addRole(new Core_Model_Acl_Role_Reviewer, 'user')
			 ->addRole(new Core_Model_Acl_Role_Chair, 'user')
			 ->addRole(new Core_Model_Acl_Role_Admin, array('user'));

		// Create whitelist
		$this->deny();

		// Admin is GOD
		$this->allow('admin', null);

		// define rules
		if (!$this->has('User')) {
			$this->add(new Core_Model_User())
				 ->allow('guest', 'User', array('login', 'logout', 'speaker'))
				 ->allow('guest', 'User', 'show', new Core_Model_Acl_ShowUserAssertion())
				 ->allow('user', 'User', array('edit', 'save'), new Core_Model_Acl_UserCanUpdateUserAssertion())
				 ->allow('chair', 'User', 'viewemail');
		}
		if (!$this->has('Session')) {
			$this->add(new Core_Model_Session())
				 ->allow('guest', 'Session', array('list', 'export'))
				 ->allow(array('presenter', 'chair', 'guest'), 'Session', array('show','subscribe', 'unsubscribe'))
				 ->allow('chair', 'Session', array('edit', 'save'), new Core_Model_Acl_UserCanUpdateSessionAssertion())
				 ->allow('chair', 'Session', array('order', 'presentationOrder'), new Core_Model_Acl_UserCanUpdateSessionAssertion())
				 ->allow('chair', 'Session', array('evaluate'));
		}
		if (!$this->has('Conference')) {
			$this->add(new Core_Model_Conference());
		}
		if (!$this->has('Submit')) {
			$this->add(new Core_Model_Submit())
				 ->allow('user', 'Submit', 'new')
				 ->allow('user', 'Submit', 'index')
				 ->allow('user', 'Submit', 'save')
				 ->allow('user', 'Submit', array('edit', 'save'), new Core_Model_Acl_UserCanUpdateSubmissionAssertion())
				 ->allow('reviewer', 'Submit', array('download', 'review', 'list'));
		}
		if (!$this->has('Review')) {
			$this->add(new Core_Model_Review())
				 ->allow('reviewer', 'Review', array('new', 'save', 'edit', 'listmine'))
				 ->allow('reviewer', 'Review', 'list', new Core_Model_Acl_UserCanListReviewsAssertion());
		}
		if (!$this->has('Location')) {
			$this->add(new Core_Model_Location())
				 ->allow('user', 'Location', 'list');
		}
		if (!$this->has('Timeslot')) {
			$this->add(new Core_Model_Timeslot())
				 ->allow('user', 'Timeslot', 'listSelect');
		}
		if (!$this->has('Presentation')) {
			$this->add(new Core_Model_Presentation())
				 ->allow('presenter', 'Presentation', array('edit', 'save', 'files', 'filesSave'), new Core_Model_Acl_UserCanUpdatePresentationAssertion())
				 ->allow('presenter', 'Presentation', 'deleteuserlink', new Core_Model_Acl_UserCanModifyUserPresentationAssertion())
				 ->allow(array('guest', 'presenter', 'chair'), 'Presentation', array('list', 'show'));
		}
		if (!$this->has('Schedule')) {
			$this->add(new Core_Model_Schedule())
				 ->allow(array('guest', 'chair', 'presenter'), 'Schedule', 'list');
		}
		if (!$this->has('Event')) {
			$this->add(new Core_Model_Event())
				 ->allow('guest', 'Event', array('show', 'export'))
				 ->allow(array('chair', 'presenter', 'guest'), 'Event', 'list');
		}
		if (!$this->has('Poster')) {
			$this->add(new Core_Model_Poster())
				 ->allow('guest', 'Poster', array('list', 'show', 'liststudent'));
		}
		if (!$this->has('Topic')) {
			$this->add(new Core_Model_Topic())	        
				 ->allow('guest', 'Topic', array('list'));
		}	
		if (!$this->has('File')) {
			$this->add(new Core_Model_File())
				 ->allow('guest', 'File', array('getfile', 'show', 'getstaticfile'))
				 ->allow('reviewer', 'File', array('getsubmission', 'getpaper'));
		}
		if (!$this->has('Feedback')) {
			$this->add(new Core_Model_Feedback())
				 ->allow('guest', 'Feedback', 'save', new Core_Model_Acl_GuestCanSaveFeedbackAssertion())
				 ->allow('guest', 'Feedback', array('index', 'feedbacksection', 'ratepres', 'ratings', 'voteposter'));
		}
		// secure web/media
		if (!$this->has('Media')) {
			$this->add(new Web_Model_Media());
		}	
	}


}
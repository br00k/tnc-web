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
 * @revision   $Id: NavigationSelector.php 619 2011-09-29 11:20:22Z gijtenbeek $
 */
 
/**
  * Controller plugin that sets navigation object based on conference abbreviation
  *
  */ 
class TA_Controller_Plugin_NavigationSelector extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var Zend_Navigation
     */
    protected $_navigation = null;

	/**
	 * Set navigation object based on conference abbreviation
	 *
	 * @return	void
	 */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	$acl = new Core_Model_Acl_Core();
		$auth = Zend_Auth::getInstance();
		$view = Zend_Layout::getMvcInstance()->getView();

		// get user role
		$role = ($auth->hasIdentity()) ? $auth->getIdentity() : 'guest';

		// store ACL in global registry
		Zend_Registry::set('acl', $acl);

		$abbreviation = 'core';
		try {
			$conference = Zend_Registry::get('conference');
			if ($conference['navigation']) {
				$abbreviation = strtolower($conference['abbreviation']);
			}
		} catch (Exception $e) {
			throw new TA_Exception('conference key not found in registry');
		}

		$pages = require APPLICATION_PATH.'/configs/navigation/'.$abbreviation.'.php';

		$this->_navigation = new Zend_Navigation($pages);

		// view helper
		$navViewHelper = $view->navigation($this->_navigation);

        // add ACL and default role to navigation
        $navViewHelper->setAcl($acl)->setRole($role);
    }

    /**
     * Get Zend_Navigation instance
     *
     * @return Zend_Navigation
     */
    public function getNavigation()
    {
        return $this->_navigation;
    }
}
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
 * Change layout based on configuration value
 *
 * @author Christian Gijtenbeek
 * @package TA_Controller
 * @subpackage Plugin
 */
class TA_Controller_Plugin_LayoutPicker extends Zend_Controller_Plugin_Abstract {

	/**
	 * Conference abbreviation
	 * @var string
	 */
	private $_abbr;

	/**
	 * Conference info
	 * @var array
	 */
	private $_conference;

	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{
		// @todo, this can be removed when all instances where this is called from are removed
		if ($request->getParam('format') == 'json') {
			return Zend_Layout::getMvcInstance()->disableLayout();
		}
		$this->_abbr = 'core';

		if (Zend_Registry::isRegistered('conference')) {
			$this->_conference = Zend_Registry::get('conference');
			if ($this->_conference['layout']) {
				$this->_abbr = strtolower($this->_conference['abbreviation']);
			}
		}

		// if you call setLayout() from your controller, make sure to also assign 'customlayout' to true
		if (!Zend_Layout::getMvcInstance()->customlayout) {
			// see note at top, this makes the ugly 'json' check obsolete
			if (Zend_Layout::getMvcInstance()->isEnabled()) {
				Zend_Layout::getMvcInstance()->setLayout($this->_abbr.'/main');
			}
		}

		$this->_initView();
	}

	/**
	 * Helper method to initialize view
	 * @return void
	 */
	private function _initView()
	{
		$cssFolder = ($this->_conference['layout'])
			? '/includes/'.strtolower($this->_conference['abbreviation']).'/css/'
			: '/includes/core/css/';

		$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$view = $bootstrap->getResource('view');

		$view->doctype('XHTML1_STRICT');
		$view->headTitle(strtoupper($this->_abbr));
		$view->headScript()->prependFile('/js/jquery-ui/js/jquery.min.js');
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
						 ->appendHttpEquiv('Content-Language', 'en-US');
		$view->headLink()->prependStylesheet($cssFolder.'base.css')
						 ->headLink(array('rel' => 'favicon', 'href' => '/favicon.ico', 'PREPEND'));
		$view->headTitle()->setSeparator(' - ');
	}


}
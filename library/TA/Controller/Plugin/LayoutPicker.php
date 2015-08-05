<?php
/**
 * Change layout based on config
 *
 */
class TA_Controller_Plugin_LayoutPicker extends Zend_Controller_Plugin_Abstract {

	private $_abbr;

	private $_conference;

	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{
		// leave this in here for now, because I need to find instances where this is called from!
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
			// see note at top, this makes the 'json' check obsolete
			if (Zend_Layout::getMvcInstance()->isEnabled()) {
				Zend_Layout::getMvcInstance()->setLayout($this->_abbr.'/'.$this->_abbr);
			}
		}

		$this->_initView();
	}

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
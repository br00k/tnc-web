<?php

class Core_View_Helper_Javascript extends Zend_View_Helper_Abstract
{

	/**
	 * Include namespaced javascript
	 * If custom layout is used, the javascript looks in the /includes/abbreviation/js/ folder
	 * else it looks in /includes/core/js/
	 */
	public function javascript($link)
	{
		if (!$link) {
			return false;
		}
		$conference = Zend_Registry::get('conference');
		$namespace = ($conference['layout']) 
			? strtolower($conference['abbreviation'])
			: 'core';
		$this->view->headScript()->appendFile('/includes/'. $namespace . '/js/'. $link);
	}



}
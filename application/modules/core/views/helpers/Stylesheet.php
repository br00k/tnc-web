<?php

class Core_View_Helper_Stylesheet extends Zend_View_Helper_Abstract
{

	/**
	 * Include namespaced stylesheet
	 * If custom layout is used, the stylesheet looks in the /includes/abbreviation/css/ folder
	 * else it looks in /includes/core/css/
	 */
	public function stylesheet($link)
	{
		if (!$link) {
			return false;
		}
		$conference = Zend_Registry::get('conference');
		$namespace = ($conference['layout']) 
			? strtolower($conference['abbreviation'])
			: 'core';
		$this->view->headLink()->appendStylesheet('/includes/'. $namespace . '/css/'. $link);
	}



}
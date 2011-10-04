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
 * @revision   $Id: Stylesheet.php 598 2011-09-15 20:55:32Z visser $
 */

/**
 * Include stylesheet Info View helper
 *
 * @package Core_View_Helper
 */
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
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
 * @revision   $Id: Javascript.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */

/**
 * Include javascript view helper
 *
 * @package Core_View_Helper
 */
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
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
 * @revision   $Id: ImageResize.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */
require_once dirname(dirname(__FILE__)).'/phpthumb/ThumbLib.inc.php';

/**
 * HTMLPurifier
 *
 * @author Christian Gijtenbeek
 * @package TA_Filter
 */ 
class TA_Filter_HTMLPurifier implements Zend_Filter_Interface
{
	protected $_purifier;

	public function __construct($options = null)
	{
		HTMLPurifier_Bootstrap::registerAutoLoad();
		$config = HTMLPurifier_Config::createDefault();
		$config->set('HTML.Strict', true);
		$config->set('Attr.EnableID', true);
		$config->set('Attr.IDPrefix', 'MyPrefix_');
		$this->_purifier = new HTMLPurifier($config);		
	}

	/**
	 * 
	 *
	 */
	public function filter($value)
	{
		return $this->_purifier->purify($value);
	}



}

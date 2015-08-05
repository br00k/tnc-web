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
 * @revision   $Id: Abstract.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */

class TA_Model_Abstract {

	/**
	 * @var array Model resource instances
	 */
	protected $_resources = array();

	/**
	 * @var array Form instances
	 */
	protected $_forms = array();

	/**
	 * Returns form instance
	 */
	public function getForm($name)
	{
		if (!isset($this->_forms[$name])) {
    		$class = implode( '_', array($this->_getNamespace(), 'Form', $this->_getInflected($name) ));
    		$this->_forms[$name] = new $class();
		}
		return $this->_forms[$name];
	}

	/**
	 * Lazy load resource class
	 */
	public function getResource($name)
	{
		if ( !isset($this->_resources[$name]) ) {
    		$class = implode( '_', array($this->_getNamespace(), 'Resource', $this->_getInflected($name) ));
    		$this->_resources[$name] = new $class();
		}
		return $this->_resources[$name];
	}

	private function _getNameSpace()
	{
		$ns = explode('_', get_class($this));
		return $ns[0];
	}

	private function _getInflected($name)
	{
		$inflector = new Zend_Filter_Inflector(':class');
		$inflector->setRules(array(
			':class'  => array('Word_CamelCaseToUnderscore')
		));
		return ucfirst($inflector->filter(array('class' => $name)));
	}

}

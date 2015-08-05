<?php

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

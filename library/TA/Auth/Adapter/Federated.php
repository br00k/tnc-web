<?php
require_once('simplesaml/lib/_autoload.php');

class TA_Auth_Adapter_Federated implements Zend_Auth_Adapter_Interface
{

	/**
	 * Identity value
	 * @var string
	 */
	private $_id = null;
	
	private $_simpleSaml = null;

	/**
	 * Authentication source id, defined in simplesaml/config/authsources.php 
	 * @var string
	 */
	private $_authSource = null;

	/**
	 * Constructor
	 *
	 * @param	string	$authSource ID of the authentication source that should be used
	 * @return	void
	 */
	public function __construct($authSource)
	{
		$this->_authSource = $authSource;
	}

    /**
     * Sets the value to be used as the identity
     *
     * @param  string $id the identity value
     * @return Zend_Auth_Adapter_Federated Provides a fluent interface
     */
    public function setIdentity($id)
    {
        $this->_id = $id;
        return $this;
    }
    
    public function getLogoutUrl()
    {
    	return $this->_simpleSaml->getLogoutUrl();
    }

    /**
     * Authenticates the given Federated identity.
     * Defined by Zend_Auth_Adapter_Interface.
     *
     * @throws Zend_Auth_Adapter_Exception If answering the authentication query is impossible
     * @return Zend_Auth_Result or TA_Auth_Result_Federated
     */
	public function authenticate()
	{
		$this->_simpleSaml = new SimpleSAML_Auth_Simple($this->_authSource);

		$id = $this->_id;

		try {
			$this->_simpleSaml->requireAuth();
			$attributes = $this->_simpleSaml->getAttributes();
			
			if ($this->_simpleSaml->isAuthenticated()) {
				return new TA_Auth_Result_Federated(Zend_Auth_Result::SUCCESS, $attributes);
			} else {
				return new TA_Auth_Result_Federated(Zend_Auth_Result::FAILURE, $attributes, array("Authentication failed"));
			}

		} catch (Exception $e) {
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $id, array("Authentication failed"));
		}

	}

}
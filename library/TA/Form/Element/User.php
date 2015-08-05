<?php
/**
 *
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class TA_Form_Element_User extends Zend_Form_Element_Select
{

	/**
	 * Holds the row object
	 * @var	TA_Model_Resource_Db_Table_Row_Abstract
	 */
	protected $_taRow;

	protected $_taController;

	/**
	 * @todo: replace this method by an Ajax call
	 *
	 */
	public function init()
	{
		#$this->populateElement();
	}

	/**
	 * Populate element with user values
	 * @param	string	$role	Only show users that have this role
	 * @return	TA_Form_Element_User	fluent interface
	 */
	public function populateElement($role = null)
	{
        $userModel = new Core_Model_User();
		$this->setMultiOptions($userModel->getUsersForSelect(true, $role))
			 ->setRegisterInArrayValidator(false);
		return $this;
	}

	/**
	 * Set row property for later access by decorator
	 *
	 * @param	TA_Model_Resource_Db_Table_Row_Abstract		$row
	 * @return	TA_Form_Element_User	fluent interface
	 */
	public function setTaRow(TA_Model_Resource_Db_Table_Row_Abstract $row)
	{
		$this->_taRow = $row;
		return $this;
	}

	public function setTaController($controller)
	{
		$this->_taController = $controller;
		return $this;
	}

	public function getTaController()
	{
		return $this->_taController;
	}

	/**
	 * Get row object
	 *
	 * @return	TA_Model_Resource_Db_Table_Row_Abstract
	 */
	public function getTaRow()
	{
		return $this->_taRow;
	}

    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
        	$this->addDecorator('User')
            	 ->addDecorator('ViewHelper')
                 ->addDecorator('Label', array('class'=>'desc'))
                 ->addDecorator('Errors')
                 ->addDecorator('HtmlTag', array('tag'=>'li'))
                 ->addDecorator('Description', array('tag' => 'div',
                 'class' => 'description', 'escape' => false));
        }



    }


}
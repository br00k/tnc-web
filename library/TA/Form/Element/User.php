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
 * @revision   $Id$
 */
 
/**
 * Custom User form element
 *
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 * @package TA_Form
 * @subpackage Element
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
	 *
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
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
	 * @revision   $Id: Mail.php 25 2011-10-04 20:46:05Z visser@terena.org $
	 */

/** 
 *
 * @package Core_Forms
 * @subpackage Core_Forms_Submit
 */
class Core_Form_Submit_Mail extends TA_Form_Abstract
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/submit/mail');
		$this->setAttrib('id', 'mailform');
		
		$status = new Zend_Form_Element_Hidden('id');
		$status->setRequired(true)
			   ->setLabel('id')
			   ->addValidators(
				  array('Int')
			   )
			   ->setDecorators(array('Composite'));  
	    	     
		$dummy = new Zend_Form_Element_Checkbox('dummy');
		$dummy->setLabel('Do a test run (does not send emails)')
			  ->setChecked(true)
			  ->setDecorators(array('Composite'));  	    	   	      

			 
		$this->addElements(array(
			$status,
			$dummy
		));

		$this->addElement('submit', 'submit', array(
			'label' => 'Send emails (this may take a while)',
			'decorators' => $this->_buttonElementDecorator
		));

	}


}
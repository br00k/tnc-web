<?php

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
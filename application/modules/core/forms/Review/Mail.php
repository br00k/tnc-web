<?php

class Core_Form_Review_Mail extends TA_Form_Abstract
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/review/mail');
		$this->setAttrib('id', 'mailform');

	    $dummy = new Zend_Form_Element_Checkbox('dummy');
	    $dummy->setLabel('Do a test run (does not send emails)')
			  ->setChecked(true)
			  ->setDecorators(array('Composite'));

		$this->addElements(array(
			$dummy
		));

	    $this->addElement('submit', 'submit', array(
			'label' => 'Send emails (this may take a while)',
			'decorators' => $this->_buttonElementDecorator
	    ));

	}


}
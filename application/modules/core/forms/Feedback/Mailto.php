<?php

class Core_Form_Feedback_Mailto extends TA_Form_Abstract
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/feedback/mailto');
		$this->setAttrib('id', 'mailtoform');

	    $email = new Zend_Form_Element_Text('email');
	    $email->setLabel('Email address')
	    	  ->setRequired(true)
	    	  ->setAttrib('class', 'medium')
	    	  ->addValidators(array(
				array('EmailAddress', true)
	    	  ))
	    	  ->setDecorators(array('Composite'));

		$this->addElements(array(
			$email
		));

	    $this->addElement('submit', 'submit', array(
			'label' => 'Send email',
			'decorators' => $this->_buttonElementDecorator
	    ));

	}


}
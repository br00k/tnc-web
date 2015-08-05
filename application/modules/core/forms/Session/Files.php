<?php

class Core_Form_Session_Files extends TA_Form_Abstract
{

	public function init()
	{
		parent::init();

		$this->setAction('/core/session/files');
	    $this->setAttrib('enctype', 'multipart/form-data');

	    $id = new Zend_Form_Element_Hidden('session_id');
	    $id->setRequired(true)
		   ->addValidators(
		      array('Int')
		   )
		   ->setDecorators(array('Composite'));

		// the name of this form element must be of an existing filetype
	    $file1 = new TA_Form_Element_MagicFile('slides');
	    $file1->setLabel('Session slide')
			  ->setDescription('')
			  ->addDecorators($this->_magicFileElementDecorator)
			  ->setValueDisabled(true)
			  ->addValidators(array(
			      array('Count', true, 1),
			      array('Size', true, array('max' => '4Mb'))
			  ));

		$subForm = new Zend_Form_SubForm();
		$subForm->addElements(array(
	    	$file1		
		))->setDecorators(array('FormElements'));
		
		$this->addSubForm($subForm, 'files');
	    $this->addElements(array(
	    	$id	    	
	    ));

	    $this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
	    ));

	}

}
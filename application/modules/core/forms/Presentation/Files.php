<?php

class Core_Form_Presentation_Files extends TA_Form_Abstract
{

	public function init()
	{
		parent::init();

	    $this->setAttrib('enctype', 'multipart/form-data');

	    $id = new Zend_Form_Element_Hidden('presentation_id');
	    $id->setRequired(true)
		   ->addValidators(
		      array('Int')
		   )
		   ->setDecorators(array('Composite'));

	    $paper = new TA_Form_Element_MagicFile('paper');
	    $paper->setLabel('Paper')
	    	  ->setDescription('OpenOffice, PDF, and Microsoft Word are acceptable formats.')
			  ->addDecorators($this->_magicFileElementDecorator)
			  ->setValueDisabled(true)
			  ->addValidators(array(
			      array('Count', true, 1),
			      array('Size', true, array('max' => '64Mb'))
			  ));

	    $slides = new TA_Form_Element_MagicFile('slides');
	    $slides->setLabel('Slides')
	    	   ->setDescription('Microsoft Powerpoint, OpenOffice, and PDF are acceptable formats.')
			   ->addDecorators($this->_magicFileElementDecorator)
			   ->setValueDisabled(true)
			   ->addValidators(array(
			       array('Count', true, 1),
			       array('Size', true, array('max' => '64Mb'))
			   ));

	    $file1 = new TA_Form_Element_MagicFile('misc');
	    $file1->setLabel('Extra file')
			  ->setDescription('')
			  ->addDecorators($this->_magicFileElementDecorator)
			  ->setValueDisabled(true)
			  ->addValidators(array(
			      array('Count', true, 1),
			      array('Size', true, array('max' => '64Mb'))
			  ));

		$subForm = new Zend_Form_SubForm();
		$subForm->addElements(array(
	    	$paper,
	    	$slides,
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
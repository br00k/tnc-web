<?php

class Core_Form_Presentation_User extends TA_Form_Abstract
{

	public function init()
	{
		parent::init();

		$this->setAction('/core/presentation/speakers');

	    $id = new Zend_Form_Element_Hidden('presentation_id');
	    $id->setRequired(true)
		   ->addValidators(
		      array('Int')
		   )
		   ->setDecorators(array('Composite'));

		$presentationModel = new Core_Model_Presentation();

		$users = new TA_Form_Element_User('user_id');		
		$users->setTaController('presentation')
			  ->populateElement('presenter')
			  ->setAttrib('onchange', "this.form.submit()");

	    $this->addElements(array(
	    	$id,
	    	$users
	    ));

	}

}
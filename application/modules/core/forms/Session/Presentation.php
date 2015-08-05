<?php

class Core_Form_Session_Presentation extends TA_Form_Abstract
{

	public function init()
	{
		parent::init();

		$this->setAction('/core/session/show');

	    $sessionId = new Zend_Form_Element_Hidden('session_id');
	    $sessionId->setRequired(true)
	    		  ->addValidators(
				     array('Int')
				  )
				  ->setDecorators(array('Composite'));

	    $presentationModel = new Core_Model_Presentation();
	    
	    $select = new Zend_Form_Element_Select('presentation_id');
	    $select->setAttrib('onchange', 'this.form.submit()')
	    	   ->setMultiOptions($presentationModel->getPresentationsForSelect(null, '--- attach presentation ---'))
			   ->setRegisterInArrayValidator(false)
	    	   ->setDecorators(array('Composite'));

	    $this->addElements(array(
	    	$sessionId,
	    	$select
	    ));

	}

	public function setDefaults(array $values)
	{
		parent::setDefaults($values);
	}

}
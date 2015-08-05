<?php

class Core_Form_Session_User extends TA_Form_Abstract
{

	public function init()
	{
		parent::init();

		$this->setAction('/core/session/chairs');

	    $submissionId = new Zend_Form_Element_Hidden('session_id');
	    $submissionId->setRequired(true)
	    			 ->addValidators(
	    			 	array('Int')
	    			 )
	    			 ->setDecorators(array('Composite'));

	    $userModel = new Core_Model_User();

	    $select = new Zend_Form_Element_Select('user_id');
	    $select->setAttrib('onchange', 'this.form.submit()')
	    	   ->setMultiOptions($userModel->getUsersForSelect(true, 'chair'))
			   ->setRegisterInArrayValidator(false)
	    	   ->setDecorators(array('Composite'));

	    $this->addElements(array(
	    	$submissionId,
	    	$select
	    ));

	}

	public function setDefaults(array $values)
	{
		parent::setDefaults($values);
	}

}
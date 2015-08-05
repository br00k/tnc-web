<?php

class Core_Form_Submit_User extends TA_Form_Abstract
{

	public function init()
	{
		parent::init();

		$this->setAction('/core/submit/reviewers');

	    $submissionId = new Zend_Form_Element_Hidden('submission_id');
	    $submissionId->setRequired(true)
	    			 ->addValidators(
	    			 	array('Int')
	    			 )
	    			 ->setDecorators(array('Composite'));

	    $userModel = new Core_Model_User();

	    $select = new Zend_Form_Element_Select('user_id');
	    $select->setAttrib('onchange', 'this.form.submit()')
	    		// @todo Only show users that are not already reviewers for this submission
	    	   ->setMultiOptions($userModel->getUsersForSelect(true, 'reviewer'))
			   ->setRegisterInArrayValidator(false)
	    	   ->setDecorators(array('Composite'));

	    $this->addElements(array(
	    	$submissionId,
	    	$select
	    ));

	    #$this->addElement('submit', 'submit', array(
		#   'label' => 'Link users to submission',
		#   'ignore' => true,
		#   'decorators' => $this->_buttonElementDecorator
	    #));

	}

	public function setDefaults(array $values)
	{
		parent::setDefaults($values);
	}

}
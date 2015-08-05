<?php

class Core_Form_Submit_Status extends TA_Form_Abstract
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/review/list');

	    $submissionId = new Zend_Form_Element_Hidden('submission_id');
	    $submissionId->setRequired(true)
	    			 ->setLabel('submission_id')
	    			 ->addValidators(
	    			 	array('Int')
	    			 )
	    			 ->setDecorators(array('Composite'));

		// todo: replace with 'new' way of doing this
		$statusOptions = Zend_Registry::get('formconfig')->formdefaults->submit->status->toArray();

		$status = new Zend_Form_Element_Select('status');
	    $status->setLabel('Status')
		      ->setAttrib('class', 'small')
		      ->addFilter('Null')
		      ->addMultiOptions($statusOptions)
 		      ->setDecorators(array('Composite'));

 		$sessionModel = new Core_Model_Session();
		$sessions = $sessionModel->getSessionsForSelect('');

 		$session = new Zend_Form_Element_Select('session_id');
	    $session->setLabel('Proposed Session')
				->setAttrib('class', 'small')
				->addMultiOption('', '--- select a session ---')
				->addFilter('Null') // add this if you want to provide a blank value
				->addMultiOptions($sessions)
				->setDecorators(array('Composite'));

		$this->addElements(array(
			$submissionId,
			$status,
			$session
		));

	    $this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
	    ));
	}

	public function getValues($suppressArrayNotation = false)
	{
		$values = parent::getValues($suppressArrayNotation);
		if ($values['session_id'] == 0) {
			$values['session_id'] = null;
		}
		return $values;
	}


}
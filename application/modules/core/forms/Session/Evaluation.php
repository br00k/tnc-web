<?php

class Core_Form_Session_Evaluation extends TA_Form_Abstract
{

	public function init()
	{
		parent::init();

		$this->setAction('/core/session/evaluate/format/html');
		$this->setAttrib('id', 'evaluateform');

	    $evaluationId = new Zend_Form_Element_Hidden('session_evaluation_id');
	    $evaluationId->addValidator('Int')
					 ->addFilter('Null')
					 ->setDecorators(array('Composite'));
					 
	    $sessionId = new Zend_Form_Element_Hidden('session_id');
	    $sessionId->setRequired(true)
	    		  ->addValidator('Int')
				  ->setDecorators(array('Composite'));					 

	    $comments = new Zend_Form_Element_Textarea('comments');
	    $comments->setLabel('Comments')
	    		 ->setAttrib('class', 'small')
	    		 ->setRequired(true)
				 ->setDecorators(array('Composite'));
				 
	    $attendees = new Zend_Form_Element_Text('attendees');
	    $attendees->setLabel('Attendees')
				  ->setAttrib('class', 'tiny')
				  ->addValidator('Int')
				  ->addFilter('Null') // add this if you want to provide a blank value
				  ->setRequired(false)
				  ->setDecorators(array('Composite'));	
			 	 
	    $this->addElements(array(
	    	$evaluationId,
	    	$sessionId,
	    	$comments,
	    	$attendees
	    ));

	    $this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
	    ));
	}

}
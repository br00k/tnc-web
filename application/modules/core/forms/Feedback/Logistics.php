<?php

class Core_Form_Feedback_Logistics extends TA_Form_Abstract
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/feedback/logistics');

	    $id = new Zend_Form_Element_Hidden('id');
	    $id->setRequired(true)
	       ->setLabel('id')
		   ->addValidators(
			   array('Int')
		   )
		   ->setDecorators(array('Composite'));

		$elements = array(
			'website' => 'Conference website',
			'core' => 'Conference software (CORE) usability',
			'social_media' => 'Conference social media (usefulness)',
			'registration' => 'Registration / payment procedures',
			'hotel_booking' => 'Hotel booking procedures',
			'doc_during' => 'Onsite information',
			'venue' => 'Venue',
			'network' => 'Networking facilities',
			'catering' => 'Catering',
			'social_events' => 'Social events'
			#'vfm_accomodation' => 'Accommodation: did you get value for money?',
			#'vfm_regfee' => 'Registration fee: did you get value for money?'
		);

		// add all elements in loop, since they are all the same
		foreach ($elements as $name => $label) {
	    	$newSelect = new Zend_Form_Element_Radio($name);
	    	$newSelect->setLabel($label)
					  ->setAttrib('class', 'tiny')
					  //->setOptions(array("listsep" => ' '))
					  ->setMultiOptions($this->_getFieldValues('rating', 'feedback'))
					  ->setDecorators(array('Composite'));

	    	$newText = new Zend_Form_Element_Text('remarks_'.$name);
	    	$newText->setDescription('Comments')
					->setAttrib('class', 'medium')
					->setDecorators(array('Composite'));

			$this->addElements(array($newSelect, $newText));
		}

	    $accomodation = new Zend_Form_Element_Radio('vfm_accomodation');
	    $accomodation->setLabel('Accommodation')
					 ->setAttrib('class', 'tiny')
					 //->setOptions(array("listsep" => ' '))
					 ->setMultiOptions(array(
					 	'good' => 'Good value for money',
					 	'reasonable' => 'Reasonable value for money',
					 	'poor' => 'Poor value for money'
					 ))
					 ->setDecorators(array('Composite'));
					 
		$regfee = clone($accomodation);
		$regfee->setName('vfm_regfee')
			   ->setLabel('Registration fee');

	    $comments = new Zend_Form_Element_Textarea('comments');
	    $comments->setLabel('Comments on the logistical arrangements')
	    	 	 ->setAttrib('class', 'medium')
	    	 	 ->setDescription('Please limit your comments to 1000 characters')
	    	 	 ->setRequired(false)
	    	 	 ->addValidator('StringLength', true, array(1, 5000,
	    	 	 	'messages' => array(
			 	 		Zend_Validate_StringLength::TOO_SHORT => 'Please provide a longer comment',
			 	 		Zend_Validate_StringLength::TOO_LONG => 'Your comment is too long'
			 	 	)
	    	 	 ))
			 	 ->setDecorators(array('Composite'));

		$this->addElements(array(
			$id,
			$accomodation,
			$regfee,
			$comments
		));
		
	    $this->addElement('submit', 'submit', array(
			'decorators' => $this->_buttonElementDecorator
	    ));
	}


}
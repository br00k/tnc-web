<?php

class Core_Form_Feedback_Participant extends TA_Form_Abstract
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/feedback/participant');

	    $id = new Zend_Form_Element_Hidden('id');
	    $id->setRequired(true)
	       ->setLabel('id')
		   ->addValidators(
			   array('Int')
		   )
		   ->setDecorators(array('Composite'));

		$country = new TA_Form_Element_Country('country');
		$country->setLabel('Please select the country in which you work as primary place of employment.')
	    		->setDecorators(array('Composite'));

	    $orgType = new Zend_Form_Element_Select('org_type');
	    $orgType->setLabel('Please select the type of organisation that most closely resembles your primary place of employment.')
				->setAttrib('class', 'medium')
				->setMultiOptions(array(
					'0' => '---',
					'nren' => 'National Research and Education Network (NREN)',
					'high' => 'Higher / Further Education Institute (universities / college / polytechnic...)',
					'ari' => 'Academic Research Institute',
					'project' => 'Research project',
					'admin' => 'Administrative departments of academic institutions',
					'local' => 'Local / regional / central government department',
					'cultural' => 'Cultural organisation (galeries, librairies, museums, etc.)',
					'comm' => 'Commercial organisation',
					'other' => 'Other non-profit (specify)'
				))
				->setDecorators(array('Composite'));

	    $orgOther = new Zend_Form_Element_Text('org_type_other');
	    $orgOther->setDescription('Other, please specify')
				 ->setAttrib('class', 'medium')
				 ->setDecorators(array('Composite'));

	    $occupation = new Zend_Form_Element_Select('occupation');
	    $occupation->setLabel('Please select the title that most closely resembles your primary role in the organisation.')
				   ->setAttrib('class', 'medium')
				   ->setMultiOptions(array(
					   '0' => '---',
				       'director' => 'Director (responsible for overall organisational management)',
				       'manager' => 'Technical Manager',
				       'admin' => 'Administrative / Operational Manager',
				       'tech' => 'Technical staff / Engineer',
				       'res' => 'Researcher / Scientist',
				       'prof' => 'Professor / Teacher',
				       'pr' => 'Public Relations / Communications',
				       'bizz' => 'Business Development',
				       'stud' => 'Student',
				       'other' => 'Other (specify)'
				   ))
				   ->setDecorators(array('Composite'));

	    $occOther = new Zend_Form_Element_Text('occupation_other');
	    $occOther->setDescription('Other, please specify')
				 ->setAttrib('class', 'medium')
				 ->setDecorators(array('Composite'));

	    $interest = new Zend_Form_Element_MultiCheckbox('interest');
	    $interest->setLabel('Please select your main areas of interest. Up to three selections are possible.')
				 ->setAttrib('class', 'tiny')
				 ->setMultiOptions(array(
				     'sec' => 'Network security (incident, prevention and response)',
				     'nom' => 'Network operations management',
				     'clouds' => 'Storage and clouds',
				     'grids' => 'Grids',
				     'media' => 'Media management and distribution',
				     'auth' => 'Authentication and Authorisation systems and federations',
				     'wireless' => 'Fixed & mobile wireless and roaming technologies',
				     'vid' => 'Video / web-based conferencing',
				     'reg' => 'Regulatory issues including privacy',
				     'pr' => 'PR / communications / business development',
				     'strat' => 'Strategic development: European policy setting and / or organisational management',
				     'other' => 'Other (specify)'
				 ))
				 // custom validator to ensure no more than 3 checkboxes are checked
				 ->addValidator('Callback', true, array(
				 	'callback' => function($value, $arr) {
				 		return ( count($arr['interest']) > 3 ) ? false : true;
				 	},
				 	'messages' => array(
				 		Zend_Validate_Callback::INVALID_VALUE => "Please don't select more than three options"
				 	)
				 ))
				 ->setDecorators(array('Composite'));

	    $intOther = new Zend_Form_Element_Text('interest_other');
	    $intOther->setDescription('Other, please specify')
				 ->setAttrib('class', 'medium')
				 ->setDecorators(array('Composite'));

		$this->addElements(array(
			$id,
			$country,
			$orgType,
			$orgOther,
			$occupation,
			$occOther,
			$interest,
			$intOther
		));

	    $this->addElement('submit', 'submit', array(
			'decorators' => $this->_buttonElementDecorator
	    ));
	}

	/**
	 * Override method to unserialize multiCheckbox values
	 *
	 */
	public function setDefaults(array $defaults)
	{
		$defaults['interest'] = (isset($defaults['interest']) ) ? unserialize($defaults['interest']) : null;
		parent::setDefaults($defaults);
	}


}
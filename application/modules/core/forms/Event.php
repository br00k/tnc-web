<?php
/**
 *
 */
class Core_Form_Event extends TA_Form_Abstract
{

	public function init()
	{
	    $this->setAction('/core/event/new');
	    $this->setAttrib('enctype', 'multipart/form-data');

		$eventModel = new Core_Model_Event();

	    $category = new Zend_Form_Element_Select('category_id');
	    $category->setLabel('Category')
	    		 ->setMultiOptions($eventModel->getCategories())
				 ->setAttrib('class', 'medium')
				 ->setDecorators(array('Composite'));

	    $title = new Zend_Form_Element_Text('title');
	    $title->setLabel('Title')
			  ->setRequired(true)
			  ->addValidator('regex', true, array(
	    	  	 'pattern' => '/^.*$/',
	    	  	 'messages' => array(Zend_Validate_Regex::NOT_MATCH => 'Wrong format')
	    	  ))
	    	  ->addValidator(new Zend_Validate_Db_NoRecordExists(array(
	    	  	'table' => 'sessions',
	    	  	'field' => 'title'
	    	  )))
	    	  ->setAttrib('class', 'medium')
	    	  ->setDescription('Must be between 2 and 30 characters, only letters, numbers and spaces allowed')
	    	  ->setDecorators(array('Composite'));

	    $desc = new Zend_Form_Element_Textarea('description');
	    $desc->setLabel('Description')
	    	 ->setAttrib('class', 'medium')
	    	 ->setDescription('Please don\'t make your description too long')
	    	 ->setRequired(false)
	    	 ->addValidator('StringLength', true, array(1, 5000,
	    	 	'messages' => array(
					Zend_Validate_StringLength::TOO_SHORT => 'Please provide a longer description',
					Zend_Validate_StringLength::TOO_LONG => 'Your description is too long'
				)
	    	 ))
			 ->setDecorators(array('Composite'));

		$locationModel = new Core_Model_Location();

	    $location = new Zend_Form_Element_Select('location_id');
	    $location->setLabel('Location')
	    		 ->setDescription('<a href="/core/location/new/type/2">Add a new location</a>')
	    		 ->setMultiOptions($locationModel->getLocationsForSelect())
				 ->setAttrib('class', 'medium')
				 ->setDecorators(array('Composite'));

	    $closed = new Zend_Form_Element_Checkbox('closed');
	    $closed->setLabel('Closed meeting')
	    	   ->setRequired(false)
			   ->setDecorators(array('Composite'));

	    $cancelled = new Zend_Form_Element_Checkbox('cancelled');
	    $cancelled->setLabel('Meeting cancelled')
	    		  ->setRequired(false)
				  ->setDecorators(array('Composite'));

	    $registration = new Zend_Form_Element_Text('registration');
	    $registration->setLabel('Registration link')
			  ->addValidator('Url')
	    	  ->setAttrib('class', 'medium')
	    	  ->setDescription('Please supply a valid Url')
	    	  ->setDecorators(array('Composite'));

	    $person = new Zend_Form_Element_Text('persons');
	    $person->setLabel('Persons')
	    	   ->setAttrib('class', 'medium')
	    	   ->setDescription('Please add person(s)')
	    	   ->setDecorators(array('Composite'));

	    $start = new Zend_Form_Element_Text('tstart');
	    $start->setLabel('Start')
	    	  ->setDescription('dd/MM/yyyy HH:mm - 23/11/2011 14:30')
	    	  ->setAttrib('class', 'medium')
	    	  ->addFilter('Null')
	    	  ->setRequired(false)
			  ->setDecorators(array('Composite'));

	    $end = new Zend_Form_Element_Text('tend');
	    $end->setLabel('End')
	    	->setDescription('dd/MM/yyyy HH:mm')
	    	->setAttrib('class', 'medium')
	    	->addFilter('Null')
	    	->addValidator('DateIsLater', false, array('tstart'))
	    	->setRequired(false)
			->setDecorators(array('Composite'));

		$resize = new TA_Filter_ImageResize();
		$resize->setWidth(260)
			   ->setHeight(170);

	    $image = new TA_Form_Element_MagicFile('file');
	    $image->setLabel('Image')
	    	  ->setDescription('This image will show on the event details page')
			  ->addDecorators($this->_magicFileElementDecorator)
			  ->addFilter($resize)
			  ->addValidators(array(
			      array('Count', true, 1),
			      array('IsImage', true),
			      array('Size', true, array('max' => '5Mb')),
			      array('ImageSize', true, array(
					'minwidth' => 300,
					'minheight' => 200
			      ))
			  ));

		$this->addElements(array(
			$category,
			$title,
			$person,
			$start,
			$end,
			$registration,
			$desc,
			$location,
			$closed,
			$cancelled,
			$image
		));

	    $this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
	    ));
	}

}
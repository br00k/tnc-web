<?php
class Core_Form_Location extends TA_Form_Abstract
{

	public function init()
	{
	    $this->setAction('/core/location/new');

	    $name = new Zend_Form_Element_Text('name');
	    $name->setLabel('Name')
	    	 ->setRequired(true)
			 ->addValidator('regex', true, array(
	    	 	 'pattern' => '/^[a-zA-Z0-9\s]{2,100}$/',
	    	 	 'messages' => array(Zend_Validate_Regex::NOT_MATCH => 'Wrong format')
	    	 ))
	    	 ->setAttrib('class', 'medium')
	    	 ->setDescription('Must be between 2 and 100 characters, only letters, numbers and spaces allowed')
	    	 ->setDecorators(array('Composite'));

	    $abbreviation = new Zend_Form_Element_Text('abbreviation');
	    $abbreviation->setLabel('Abbreviation')
					 ->addValidator('regex', true, array(
	    			     'pattern' => '/^[a-zA-Z0-9\s]{1,10}$/',
	    			     'messages' => array(Zend_Validate_Regex::NOT_MATCH => 'Wrong format')
	    			 ))
	    			 ->addValidator(new Zend_Validate_Db_NoRecordExists(array(
	    			    'table' => 'locations',
	    			    'field' => 'abbreviation'
	    			 )))
	    			 ->setAttrib('class', 'medium')
	    			 ->setDescription('Must be between 1 and 10 characters, only letters, numbers and spaces allowed')
	    			 ->setDecorators(array('Composite'));

	    $comments = new Zend_Form_Element_Textarea('comments');
	    $comments->setLabel('Description')
	    	 	 ->setAttrib('class', 'small')
	    	 	 ->setDescription('Must be between 1 and 10 characters')
	    	 	 ->setRequired(false)
			 	 ->setDecorators(array('Composite'));

	    $capacity = new Zend_Form_Element_Text('capacity');
	    $capacity->setLabel('Capacity')
	    	 	 ->setAttrib('class', 'tiny')
	    	 	 ->addValidator('Int')
	    	 	 ->addFilter('Null')
	    	 	 ->setRequired(false)
			 	 ->setDecorators(array('Composite'));

	    $types = new Zend_Form_Element_Select('type');
	    $types->setLabel('Type')
	   		  ->setAttrib('class', 'small')
	   		  ->setRequired(true)
	   		  ->setDescription('Only locations with "room" type will be shown in the schedule')
			  ->setMultiOptions($this->_getFieldValues('types', 'location'))
			  ->setDecorators(array('Composite'));

		$address = new TA_Form_Element_Location('address');
		$address->setLabel('Address')
	   		    ->setAttrib('class', 'medium')
			    ->setDecorators(array('Composite'));		

		$resize = new TA_Filter_ImageResize();
		$resize->setWidth(90)
			   ->setHeight(68);

	    $image = new TA_Form_Element_MagicFile('file');
	    $image->setLabel('Picture')
	    	  ->setDescription('This will be the thumbnail for the live streaming box on the homepage')
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
			$types,
			$name,
			$address,
			$abbreviation,
			$comments,
			$capacity,
			$image
		));

	    $this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
	    ));
	}




}
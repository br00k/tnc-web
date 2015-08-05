<?php
 
class Core_Form_User extends TA_Form_Abstract
{

	public function init()
	{
	    $this->setAction('/core/user/new');
	    $this->setAttrib('enctype', 'multipart/form-data');

	    $fname = new Zend_Form_Element_Text('fname');
	    $fname->setLabel('First Name')
	    	  ->setRequired(true)
	    	  ->setAttrib('class', 'medium')
	    	  ->setDecorators(array('Composite'));

	    $lname = new Zend_Form_Element_Text('lname');
	    $lname->setLabel('Last Name')
	    	  ->setRequired(true)
	    	  ->setAttrib('class', 'medium')
	    	  ->setDecorators(array('Composite'));

	    $organisation = new Zend_Form_Element_Text('organisation');
	    $organisation->setLabel('Organisation')
					 ->setAttrib('class', 'medium')
					 ->setDecorators(array('Composite'));

		$country = new TA_Form_Element_Country('country');
		$country->setLabel('Country')
	    		->setDecorators(array('Composite'));

	    $jobtitle = new Zend_Form_Element_Text('jobtitle');
	    $jobtitle->setLabel('Job title')
				 ->setAttrib('class', 'medium')
				 ->setDecorators(array('Composite'));

	    $profile = new Zend_Form_Element_Textarea('profile');
	    $profile->setLabel('Biography')
				->setAttrib('class', 'medium')
				->setDecorators(array('Composite'));

	    $email = new Zend_Form_Element_Text('email');
	    $email->setLabel('Email')
	    	  ->setRequired(true)
	    	  ->setAttrib('class', 'medium')
	    	  ->setDescription('the invitation will be send to this address')
	    	  ->addValidators(array(
				array('EmailAddress', true),
				array(new Zend_Validate_Db_NoRecordExists(array(
	    	  		'table' => 'users',
	    	  		'field' => 'email'
	    	  		))
	    	  	)
	    	  ))
	    	  ->setDecorators(array('Composite'));

		$resize = new TA_Filter_ImageResize();
		$resize->setWidth(260)
			   ->setHeight(170);

	    $image = new TA_Form_Element_MagicFile('file');
	    $image->setLabel('Picture')
	    	  ->setDescription('Image should be at least 300 pixels wide and 200 pixels high, and not be over 5Mb')
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
	    	$fname,
	    	$lname,
	    	$organisation,
	    	$email,
	    	$country,
	    	$jobtitle,
	    	$profile,
	    	$image
	    ));

	    $this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
	    ));

	}

}

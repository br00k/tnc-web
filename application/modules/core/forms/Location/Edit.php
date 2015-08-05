<?php

class Core_Form_Location_Edit extends Core_Form_Location
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/location/edit');
	    $this->addElement('hidden', 'location_id', array(
			'validators' => array(
				array('Int')
			),
			'required' => true,
			'decorators' => $this->_hiddenElementDecorator
	    ));	    
	    
	}
	
	public function isValid($data)
	{
		$this->getElement('abbreviation')
		     ->getValidator('Zend_Validate_Db_NoRecordExists')
		     ->setExclude(array(
		        'field' => 'location_id',
		        'value' => $data['location_id']
		     ));

		return parent::isValid($data);
	}	

}
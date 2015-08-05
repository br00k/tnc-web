<?php

class Core_Form_Presentation_Edit extends Core_Form_Presentation
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/presentation/edit');
	    $this->addElement('hidden', 'presentation_id', array(
			'validators' => array(
				array('Int')
			),
			'required' => true,
			'decorators' => $this->_hiddenElementDecorator
	    ));
	     	    
	}

	public function isValid($data)
	{
		$this->getElement('title')
		     ->getValidator('Zend_Validate_Db_NoRecordExists')
		     ->setExclude(array(
		        'field' => 'presentation_id',
		        'value' => $data['presentation_id']
		     ));

		return parent::isValid($data);
	}	
}
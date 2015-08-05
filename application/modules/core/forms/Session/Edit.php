<?php

class Core_Form_Session_Edit extends Core_Form_Session
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/session/edit');
	    $this->addElement('hidden', 'session_id', array(
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
		        'field' => 'session_id',
		        'value' => $data['session_id']
		     ));

		return parent::isValid($data);
	}	
	
}
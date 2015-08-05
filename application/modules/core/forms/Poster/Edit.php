<?php

class Core_Form_Poster_Edit extends Core_Form_Poster
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/poster/edit');
	    $this->addElement('hidden', 'poster_id', array(
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
		        'field' => 'poster_id',
		        'value' => $data['poster_id']
		     ));

		return parent::isValid($data);
	}	

}
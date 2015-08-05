<?php

class Core_Form_User_Edit extends Core_Form_User
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/user/edit');
	    $this->addElement('hidden', 'user_id', array(
			'validators' => array(
				array('Int')
			),
			'required' => true,
			'decorators' => $this->_hiddenElementDecorator
	    ));
		$this->removeElement('password');
		$this->removeElement('passwordCheck');
		$this->removeElement('invite'); // @todo: no longer needed
		$this->removeElement('role_id'); // @todo: no longer needed
		$this->getElement('email')->setDescription(null);
		$this->getElement('organisation')
			 ->setRequired(true);
	}
	
	public function isValid($data)
	{
		$this->getElement('email')
		     ->getValidator('Zend_Validate_Db_NoRecordExists')
		     ->setExclude(array(
		        'field' => 'user_id',
		        'value' => $data['user_id']
		     ));
		
		return parent::isValid($data);
	}	


}
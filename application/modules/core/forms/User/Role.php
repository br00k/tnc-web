<?php

class Core_Form_User_Role extends TA_Form_Abstract
{

	public function init()
	{
		parent::init();

		$this->setAction('/core/user/roles');

	    $id = new Zend_Form_Element_Hidden('user_id');
	    $id->setRequired(true)
		   ->addValidators(
		      array('Int')
		   )
		   ->setDecorators(array('Composite'));

		$userModel = new Core_Model_User();

	    $roles = new Zend_Form_Element_Select('role_id');
	    $roles->setAttrib('class', 'large')
	    	  ->setAttrib('onchange', 'this.form.submit()')
			  ->setMultiOptions($userModel->getRolesForSelect())
			  ->setDecorators(array('Composite'));

	    $this->addElements(array(
	    	$id,
	    	$roles
	    ));

	}

}
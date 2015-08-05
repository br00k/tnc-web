<?php

class Core_Resource_UsersRoles extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'user_role';

	protected $_primary = 'user_role_id';

	// many to many mapping
	protected $_referenceMap = array(
		'User' => array(
			'columns' => array('user_id'),
			'refTableClass' => 'Core_Resource_Users',
			'refColumns' => array('user_id')
		),
		'Role' => array(
			'columns' => array('role_id'),
			'refTableClass' => 'Core_Resource_Roles',
			'refColumns' => array('role_id')
		)
	);	


}
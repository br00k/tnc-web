<?php

class Core_Resource_UsersSubmissions extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'users_submissions';

	protected $_primary = 'user_submission_id';

	// many to many mapping
	protected $_referenceMap = array(
		'User' => array(
			'columns' => array('user_id'),
			'refTableClass' => 'Repos_Resource_Users',
			'refColumns' => array('user_id')
		),
		'Submission' => array(
			'columns' => array('submission_id'),
			'refTableClass' => 'Repos_Resource_Submissions',
			'refColumns' => array('submission_id')
		)
	);

}
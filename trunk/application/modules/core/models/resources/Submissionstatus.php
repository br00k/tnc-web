<?php
/**
 * CORE Conference Manager
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.terena.org/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to webmaster@terena.org so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2011 TERENA (http://www.terena.org)
 * @license    http://www.terena.org/license/new-bsd     New BSD License
 * @revision   $Id$
 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Submissionstatus extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'submission_status';

	protected $_primary = 'submission_status_id';
	
	// enabling this will fuck up Observers...look into this!
	// probably because this item is not the same as the class
	#protected $_rowClass = 'Core_Resource_Submission_Item';

	public function init() {}

	/**
	 * Get submission status by submission_id
	 *
	 * @param	integer		$id		submission_id
	 * @return object Core_Resource_Submission_Item
	 */
	public function getStatusBySubmissionId($id)
	{
		return $this->fetchRow(
					$this->select()
					->where('submission_id = ?', $id)
				);
	}
	
	/**
	 * Gets submission status by primary key
	 *
	 * @return object Core_Resource_Submission_Item
	 */
	public function getSessionById($id)
	{
		return $this->find( (int)$id )->current();
	}
}
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
 * @revision   $Id: Eventlogs.php 598 2011-09-15 20:55:32Z visser $
 */
class Core_Resource_Eventlogs extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'eventlog';

	// compound primary key
	protected $_primary = array('event_type', 'conference_id');
	
	protected $_rowClass = 'Core_Resource_Eventlog_Item';
	
	protected $_rowsetClass = 'TA_Model_Resource_Db_Table_Rowset_Abstract';

	public function init() {}
	
	/**
	 * Get eventlog by primary key
	 *
	 * @return object Zend_Db_Table_Row_Abstract
	 */
	public function getEventlogById($id)
	{
		return $this->find($id, $this->getConferenceId())->current();
	}
	
	/**
	 * Get eventlog by type
	 *
	 * @return string timestamp of when event happened
	 */
	public function getTimestampByType($type)
	{
		return $this->getAdapter()->fetchOne(
			"select timestamp from " . $this->_name . " where event_type=:type and conference_id=:conference_id",
			array(
				'type' => $type, 
				'conference_id' => $this->getConferenceId()
			)
		);
	}
	
	public function getAllTimestamps()
	{
		return $this->getAdapter()->fetchAssoc(
			"select * from " . $this->_name . " where conference_id=:conference_id",
			array(
				'conference_id' => $this->getConferenceId()
			)
		);
	}
	
}
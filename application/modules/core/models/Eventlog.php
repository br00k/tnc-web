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
 * @revision   $Id: Eventlog.php 30 2011-10-06 08:37:15Z gijtenbeek@terena.org $
 */
 
/** 
 * Eventlog Model.
 * The event log keeps track of important CORE events, like sending out
 * mass emails to submitters or reviewers. This model offers a way to 
 * query the log.
 *
 * @package Core_Model
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Model_Eventlog extends TA_Model_Acl_Abstract
{

	/**
	 * Get event log by event_type
	 * @param		string		$type
	 * @return		Zend_Db_Table_Row_Abstract
	 */
	public function getItemByType($type)
	{
		$row = $this->getResource('eventlogs')->getItemByType($type);
		if ($row === null) {
			throw new TA_Model_Exception('event log type not found');
		}
		return $row;
	}

	/**
	 * Save row in eventlog table
	 *
	 * @param		array	$data	Data to save
	 * @return		mixed	The primary key of the inserted/updated record or resource error message
	 */
	public function saveEventlog(array $data)
	{
		$row = $this->getResource('eventlogs')->getEventlogById($data['event_type']);
		return $this->getResource('eventlogs')->saveRow($data, $row);
	}

	/**
	 * Get eventlog by type
	 *
	 * @return Zend_Date timestamp of when event happened
	 */
	public function getTimestampByType($type)
	{
		if ($timestamp = $this->getResource('eventlogs')->getTimestampByType($type)) {
			return new Zend_Date($timestamp, Zend_Date::ISO_8601);
		}
		return false;
	}

	/**
	 * Proxy
	 */
	public function getAllTimestamps()
	{
		return $this->getResource('eventlogs')->getAllTimestamps();
	}

}









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
 * @revision   $Id: Sessionsfiles.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Sessionsfiles extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'sessions_files';

	protected $_primary = 'session_file_id';

	public function init() {}

	/**
	 * Gets item by primary key
	 * @return object Zend_Db_Table_Row
	 */
	public function getItemById($id)
	{
		return $this->find( (int)$id )->current();
	}

	/**
	 * Gets linked files
	 *
	 * @param	integer		$id		session_id
	 * @param	boolean		$allData	Returns array with current core_filetype => file_id pairs
	 * @return	Array		array of file_id's
	 */
	public function getFilesBySessionId($id, $allData = false)
	{
		if ($allData) {
			return $this->getAdapter()->fetchPairs(
				"select f.core_filetype, f.file_id from ". $this->_name ." sf
				left join vw_files f on (sf.file_id = f.file_id) where session_id=".$id
			);	
		}
		
		return $this->getAdapter()->fetchCol(
		   "select file_id from ". $this->_name ." where session_id=:session_id",
		   array(
		   	'session_id' => $id,
		   )
		);
	}

	/**
	 * returns item based on id values
	 *
	 * @param	array	$data	Presentation_id and File_id values
	 * @return	object	Zend_Db_Table_Row
	 */
	public function getItemByValues(array $data)
	{
		return $this->fetchRow(
					$this->select()
					->where('session_id = ?', $data['session_id'])
					->where('file_id = ?', $data['file_id'])
				);
	}

	/**
	 * Save rows to the database. (insert or update)
	 *
	 * @param array $values
	 * @return	boolean
	 */
	public function saveRows($values)
	{

		$sessionId = (int) $values['session_id'];
		if ($sessionId === 0 ) {
			throw new TA_Model_Resource_Db_Table_Exception('session_id not present');
		}

		$db = $this->getAdapter();

		// array with current core_filetype => file_id pairs
		$currentValues = $db->fetchPairs(
			"select f.core_filetype, f.file_id from ". $this->_name ." sf
			left join vw_files f on (sf.file_id = f.file_id) where session_id=".$sessionId
		);

		// loop through successfully uploaded files
		foreach ($values['file_id'] as $fileType => $fileId) {

			$value['session_id'] = $sessionId;
			$value['file_id'] = $fileId;

			// Update if core_filetype already has a file associated with it
			if ( isset($currentValues[$fileType]) ) {
				// do update
				$query = "UPDATE " . $this->_name . " SET
				file_id=:file_id, session_id=:session_id
				WHERE file_id=:file_id_old";
				$value['file_id_old'] = $currentValues[$fileType];
			} else {
				// do insert
				$query = "INSERT INTO " . $this->_name . "(file_id, session_id)
				VALUES (:file_id, :session_id)";
			}

			$db->query($query, $value);
		}

		return true;
	}

}
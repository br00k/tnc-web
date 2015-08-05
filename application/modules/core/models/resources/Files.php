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
 * @revision   $Id: Files.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Files extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'files';

	protected $_primary = 'file_id';

	protected $_rowClass = 'Core_Resource_File_Item';

	protected $_rowsetClass = 'Core_Resource_File_Set';

	public function init() {}

	/**
	 * not necessary?
	 *
	 */
	public function insert(array $data)
	{
		return parent::insert($data);
	}

	/**
	 * Get file by file_id
	 * @param	integer		$id		file_id
	 * @return	Core_Resource_File_Item
	 */
	public function getFileById($id)
	{
		return $this->find( (int)$id )->current();
	}

	/**
	 * Get files by ids
	 * @param	array	$ids	Collection of file_id's
	 * @param	boolean	$object	Return Rowset object instead of array
	 * @return	array/object
	 */
	public function getFilesByIds(array $ids, $object=false)
	{
		if (empty($ids)) {
			return null;
		}
		if ($object) {
			return $this->find( $ids );
		}
		$file_ids = implode(",", $ids);
		$query = "select * from files where file_id in ($file_ids)";
		return $this->getAdapter()->fetchAssoc($query);
	}

	public function getFileByHash($hash)
	{
		return $this->fetchRow(
			$this->select()
				 ->from($this->_name, array('filename_orig', 'file_id'))
				 ->where('filehash = ?', $hash)
		);
	}

	public function getFiles($paged=null, $order=array())
	{

		$select = $this->select();

		if (!empty($order[0])) {
			$select->order($order[0].' '.$order[1]);
		}

		if ($paged) {

			$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);

			$paginator = new Zend_Paginator($adapter);
			$paginator->setCurrentPageNumber( (int)$paged )
					  ->setItemCountPerPage(5);

			return $paginator;

		}

		return $this->fetchAll($select);
	}

	/**
	*
	* @todo: replace the hardcoded mapping with a db query to filetypes!
	*/
	public function saveRow(array $data, $row = null)
	{
		$data = current($data);

		if (!is_numeric($data['_filetype'])) {
			switch ($data['_filetype']) {
				case 'paper':
					$data['_filetype'] = 4;
				break;
				case 'slides':
					$data['_filetype'] = 5;
				break;
				case 'abstract':
					$data['_filetype'] = 8;
				break;
				default:
					$data['_filetype'] = 3;
				break;
			}
		}

		return $this->insert(array(
			'filename_orig' => $data['_filename_original'],
			'filename' => substr(strrchr($data['tmp_name'], '/'), 1 ), // strip path from filename
			'filesize' => $data['size'],
			'mimetype' => $data['type'],
			'filehash' => $data['_filehash'],
			'filetype' => $data['_filetype']
		));
	}

}
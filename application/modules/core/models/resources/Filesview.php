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
 * @revision   $Id: Filesview.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Filesview extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'vw_files';

	protected $_primary = 'file_id';

	protected $_rowClass = 'Core_Resource_File_Item';

	protected $_rowsetClass = 'Core_Resource_File_Set';

	public function init() {}

	public function getFileById($id)
	{
		return $this->find((int) $id)->current();
	}
	
	/**
	 * Get files by file_id's
	 *
	 * @param	array	$ids	file_id's
	 * @return	Zend_Db_Table_Rowset
	 */
	public function getFilesByIds($ids)
	{
		return $this->find(array_filter($ids));
	}

}
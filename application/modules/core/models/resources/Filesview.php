<?php
class Core_Resource_Filesview extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'vw_files';

	protected $_primary = 'file_id';

	protected $_rowClass = 'Core_Resource_File_Item';

	protected $_rowsetClass = 'Core_Resource_File_Set';

	public function init() {}

	public function getFileById($id)
	{
		return $this->find( (int)$id )->current();
	}
	
	/**
	 * Get files by file_id's
	 *
	 * @param	array	$ids	file_id's
	 * @return	Zend_Db_Table_Rowset
	 */
	public function getFilesByIds($ids)
	{
		return $this->find( $ids );
	}

}
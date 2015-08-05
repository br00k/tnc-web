<?php
/**
 * This class represents a rowset
 */
class Core_Resource_File_Set extends Zend_Db_Table_Rowset_Abstract
{

	public function getNormalizedFiles()
	{
		foreach ($this as $row) {
			$filenames[$row->getFullFilePath()] = $row->getIndexedName();
		}
		return $filenames;
	}
}
<?php

class Core_Model_File extends TA_Model_Acl_Abstract
{

	/**
	 * Get file by id
	 * @param		integer		$id		User id
	 * @return		Core_Resource_File_Item
	 */
	public function getFileById($id)
	{
		$row = $this->getResource('filesview')->getFileById( (int) $id );
    	if ($row === null) {
    		throw new TA_Model_Exception('id not found');
    	}

		// if file is submission and user is not allowed to get the specific type
    	if ( ($row->core_filetype == 'submission') && (!$this->checkAcl('getsubmission')) ) {
			throw new TA_Model_Exception('Insufficient rights for downloading this type of file');
    	}
    	return $row;
	}

}
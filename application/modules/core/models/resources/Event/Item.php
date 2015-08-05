<?php
/**
	* This class represents a single record
	* Methods in this class could include logic for a single record
	* for example sticking first_name and last_name together in a getName() method
	*
	*/
class Core_Resource_Event_Item extends TA_Model_Resource_Db_Table_Row_Abstract
{
	// returns concatenated start/end time, eg: "09:00-11:00"
	public function getCompleteTime()
	{
		return $this->_isoToNormalDate($this->tstart, 'HH:mm') . '-' . $this->_isoToNormalDate($this->tend, 'HH:mm');
	}
	
	// returns concatenated start/end datetime, eg: "10/11/2010 09:00-11:00"
	public function getCompleteDateTime()
	{
		return $this->_isoToNormalDate($this->tstart, 'dd/MM/yyyy HH:mm') . '-' . $this->_isoToNormalDate($this->tend, 'HH:mm');
	}	
}
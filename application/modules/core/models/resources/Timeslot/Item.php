<?php
/**
	* This class represents a single record
	* Methods in this class could include logic for a single record
	* for example sticking first_name and last_name together in a getName() method
	*
	*/
class Core_Resource_Timeslot_Item extends TA_Model_Resource_Db_Table_Row_Abstract
{
	// returns concatenated start/end time, eg: "10/11/2010 09:00-11:00"
	public function getCompleteTimeslot()
	{
		return $this->_isoToNormalDate($this->tstart, 'dd/MM/yyyy HH:mm') . '-' . $this->_isoToNormalDate($this->tend, 'HH:mm');
	}
	
	public function getTimeslotTimes()
	{
		return $this->_isoToNormalDate($this->tstart, 'HH:mm') . '-' . $this->_isoToNormalDate($this->tend, 'HH:mm');
	}
	
	public function getStartDay()
	{
		return $this->_isoToNormalDate($this->tstart, Zend_Date::DATE_SHORT);
	}
}
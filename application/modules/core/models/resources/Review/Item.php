<?php
/**
	* This class represents a single record
	* Methods in this class could include logic for a single record
	* for example sticking first_name and last_name together in a getName() method
	*
	*/
class Core_Resource_Review_Item extends TA_Model_Resource_Db_Table_Row_Abstract
{
	public function getFullReviewerName() 
	{
		return htmlspecialchars($this->fname . ' ' . $this->lname . ' <'.$this->email.'>');
	}
}
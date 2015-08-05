<?php
class Core_Resource_Feedbackprogramme extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'feedback.programme';

	protected $_primary = 'id';

	protected $_rowClass = 'Core_Resource_Feedback_Item';

	public function init() {}

	/**
	 * Gets feedback by feedback id
	 * @return object Zend_Db_Table_Row
	 */
	public function getFeedbackById($id)
	{
		return $this->fetchRow($this->select()
			->where("id = ?", $id)
		);
	}
}
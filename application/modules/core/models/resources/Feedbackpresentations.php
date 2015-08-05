<?php
class Core_Resource_Feedbackpresentations extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'feedback.presentations';

	// compound primary key
	protected $_primary = array('id', 'presentation_id');

	protected $_rowClass = 'Core_Resource_Feedback_Item';

	public function init() {}

	/**
	 * Gets feedback by feedback id
	 * @return object Zend_Db_Table_Row
	 */
	public function getFeedbackById($id, $presentationId)
	{
		return $this->find($id, $presentationId)->current();
	}

	/*
	* Get all presentation ratings
	*
	* @param	integer		$codeId		Feedback code id (pk from feedback.codes)
	* @return array
	*/
	public function getPresentationRatingsByCodeId($codeId)
	{
		return $this->getAdapter()->fetchAssoc(
			$this->select()
				 ->where( 'id = ?', $codeId)
				 ->from('feedback.presentations', array('presentation_id', 'rating'))
		);
	}
}
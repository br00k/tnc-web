<?php
class Core_Resource_Feedbackcodes extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'feedback.codes';

	protected $_primary = 'code_id';

	public function init() { }

	/**
	 * Gets feedback by UUID
	 * @return object Zend_Db_Table_Row
	 */
	public function getFeedbackByUuid($uuid)
	{
		return $this->fetchRow($this->select()
			->where("uuid = ?", $uuid)
		);
	}

	/*
	 * This will create a number ($i) of feedbackcodes
	 *
	 * @param	integer 	$i			Number of codes to create
	 * @param	boolean		$delete		Delete all entries before inserting
	 * @return	array		Associative array feedback id => feedback code
	 */
	public function createFeedbackCodes($i, $delete = false)
	{
		$uuid = new TA_Uuid();

		if ($delete) {
			$this->delete('1=1'); // uh?
		}

		$return = array();

		for ($j=0; $j<$i; $j++) {
			$code = $uuid->get();
			$id = $this->insert( array('uuid' => $code) );
			$return[$id] = $code;
		}

		return $return;
	}
}
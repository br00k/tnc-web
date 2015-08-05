<?php
class Core_Resource_Sessionspresentations extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'sessions_presentations';

	protected $_primary = 'session_presentation_id';

	public function init() {}

	/**
	 * Gets item by primary key
	 * @return object Zend_Db_Table_Row
	 */
	public function getItemById($id)
	{
		return $this->find( (int)$id )->current();
	}

	/**
	 * returns item based on id values
	 * This is a particular action for many to many tables
	 *
	 * @param	array	$data	Session_id and Presentation_id values
	 * @return	object	Zend_Db_Table_Row
	 */
	public function getItemByValues(array $data)
	{
		return $this->fetchRow(
					$this->select()
					->where('session_id = ?', $data['session_id'])
					->where('presentation_id = ?', $data['presentation_id'])
				);
	}

	/**
	 * return item based on presentation_id
	 *
	 * @param	mixed	$data	array containing presentation_id or presentation_id
	 * @return	mixed	Zend_Db_Table_Row if entry exists, NULL if no item is found
	 */
	public function getItemByPresentationId($data)
	{
		$id = (is_array($data)) ? $data['presentation_id'] : (int) $data;

		return $this->fetchRow(
					$this->select()
					->where('presentation_id = ?', $id)
				);

	}
	
	/**
	 * Get current presentation order
	 *
	 * @return array	session_presentation_id ordered by displayorder
	 */
	public function getPresentationOrder($sessionId)
	{
		return $this->getAdapter()->fetchCol(
			"select session_presentation_id from sessions_presentations sp 
			where sp.session_id=".$sessionId." 
			order by displayorder asc"
		);
	}
	
	/**
	 * Update order of item
	 *
	 * @param	integer		$id		session_presentation_id
	 * @param	integer		$order	displayorder
	 * @return	void or exception
	 */
	public function updatePresentationOrder($id, $order)
	{
		$row = $this->getItemById($id);
		try {
			$this->saveRow(array('displayorder' => $order), $row); 
		} catch (Exception $e) {		
			throw new TA_Model_Resource_Db_Table_Exception($e->getMessage());
		}
		
	}

}
<?php
class Core_Resource_Sessionsview extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'vw_sessions';

	protected $_primary = 'session_id';

	protected $_rowClass = 'Core_Resource_Session_Item';

	protected $_rowsetClass = 'Core_Resource_Session_Set';

	public function init() {}

	/**
	 * Gets session by primary key
	 * @return object Core_Resource_Session_Item
	 */
	public function getSessionById($id)
	{
		return $this->find( (int)$id )->current();
	}

	/**
	 * Get sessions by datetime
	 *
	 * @param	Zend_Date		$date
	 * @return	Core_Resource_Session_Set
	 */
	public function getSessionsByDate(Zend_Date $date = null)
	{
		$date = new Zend_Date($date);
		$zd = $date->get(Zend_Date::ISO_8601);

		return $this->fetchAll(
		    $this->select()
		    	 ->where("tstart <= '".$zd."'")
		    	 ->where("tend >= '".$zd."'")
		);
	}

	/**
	 * Get sessions after datetime but only on $date day
	 *
	 * @param	Zend_Date	$date
	 * @return	Core_Resource_Session_Set
	 */
	public function getSessionsAfterDate(Zend_Date $date = null)
	{
		$date = new Zend_Date($date);
		$zd = $date->get(Zend_Date::ISO_8601);

		return $this->fetchAll(
			$this->select()
			    ->where("tstart >= '".$zd."'")
			    ->where("date_trunc('day', tstart) = (select DATE '".$zd."')")
			    ->order('tstart')
			);
	}

	/**
	 * Get sessions before datetime
	 *
	 * @param	Zend_Date	$date
	 * @return	Core_Resource_Session_Set
	 */
	public function getSessionsBeforeDate(Zend_Date $date = null)
	{
		$date = new Zend_Date($date);
		$zd = $date->get(Zend_Date::ISO_8601);

		return $this->fetchAll(
			$this->select()
			    ->where("tend <= '".$zd."'")
			    ->order('tstart')
			    ->order('location_abbreviation')
			);
	}

	/**
	 * Get sessions by session_id's
	 *
	 * @param	$submissions
	 */
	public function getSessionsByIds(array $submissions)
	{
		$sessionIds = array();

		foreach ($submissions as $submission) {
			if ($submission['session_id']) {
				$sessionIds[] = (int) $submission['session_id'];
			}
		}
		$sessionIds = implode(',', $sessionIds);

		if (!empty($sessionIds)) {
			return $this->fetchAll(
				$this->select()
					 ->where('session_id IN ('.$sessionIds.')')
			);
		}
		return false;

	}

	/**
	 * Testing google sync
	 * @todo: remove if not needed
	 */
	public function getAllSessionData()
	{
		return $this->fetchAll(
			$this->select()
		);
	}

}
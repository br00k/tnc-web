<?php

class TA_Model_Resource_Db_Table_Abstract extends Zend_Db_Table_Abstract {

	/**
	* @var array
	*/
	protected $_observers = array();

	/**
	* @var integer
	*/
	private $_conferenceId;

	/**
	 *
	 *
	 */
	public function init()
	{
		$this->getConferenceId();
	}

	/**
	 *
	 *
	 */
	public function getConferenceId()
	{
      if (null === $this->_conferenceId) {
      		$conference = Zend_Registry::get('conference');
            $this->setConferenceId($conference['conference_id']);
        }
        return $this->_conferenceId;
	}

	/**
	 *
	 *
	 */
	public function setConferenceId($id)
	{
		$this->_conferenceId = $id;
		return $this;
	}

	/**
	 * Save a row to the database
	 *
	 * @param	array	$data The data to insert/update
	 * @param	Zend_Db_Table_Row $row optional The default row to use
	 *
	 * @return mixed The primary key of the inserted/updated record
	 */
	public function saveRow(array $data, $row = null)
	{

		if ($row === null) {
			$row = $this->createRow();
		}

		$metadata = $this->info('metadata');

		// automatically add conference_id value
		if ( isset($metadata['conference_id'])
		 	&& !isset($data['conference_id'])
			&& !$row instanceof Core_Resource_Conference_Item
			) {
			$data['conference_id'] = $this->getConferenceId();
		}

		foreach ($metadata as $column => $meta) {
			if (array_key_exists($column, $data)) {

				// serialize array values (mutiCheckbox)
				if ( is_array($data[$column]) ) {
					$data[$column] = serialize($data[$column]);
				}

				// transform date values to Zend_Date objects
				if ($meta['DATA_TYPE'] == 'timestamptz') {
					// ignore sql expressions like now()
					if ( !strpos($data[$column], '(') ) {
						$zd = new Zend_Date($data[$column], 'dd/MM/yyyy HH:mm');
						$data[$column] = $zd->get(Zend_Date::ISO_8601);
					}
				}

				$row->$column = $data[$column];

			}
		}

		return $row->save();
	}

	/**
	* Add an Observer object
	* This proxies to the Row object
	*
	* @param object $o Observer that implements the iObserver interface
	* @retunn void
	*/
	public function attachObserver(TA_Model_Observer_Interface $o)
	{
		$row = $this->getRowClass();
		$row::attachStaticObserver($o);
	}

	/**
	* Remove an observer object from the instance
	* This proxies to the Row object
	*
	* @param object $o Observer that implements the iObserver interface
	* @retunn void
	*/
	public function detachObserver(TA_Model_Observer_Interface $o)
	{
		$row = $this->getRowClass();
		$row::detachStaticObserver($o);
	}


}
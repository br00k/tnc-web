<?php
/**
 * Eventlog observer.
 *
 * I can implement the following methods here: _postSent
 *
 */
class Core_Model_Observer_Eventlog implements TA_Model_Observer_Interface
{

	private $_type;

	public function __construct($type)
	{
		$this->_type = $type;
	}

	public function _postSent(TA_Model_Observed_Interface $subject, $msg)
	{
		$eventlogModel = new Core_Model_Eventlog();
		$eventlogModel->saveEventlog(array(
		    'event_type' => $this->_type,
		    'timestamp' => 'now()'
		));
	}
}
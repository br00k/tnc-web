<?php
/**
 * Conference Info Helper
 *
 * @author Christian Gijtenbeek
 */
class TA_Controller_Action_Helper_ConferenceInfo extends Zend_Controller_Action_Helper_Abstract
{
	protected $_conference;

	public function conferenceInfo($info)
	{
		if (!$this->_conference) {
			$this->_conference = Zend_Registry::get('conference');
		}

		if ($info) {
			return $this->_conference[$info];
		} else {
			return $this;
		}
	}


    /**
	 * Proxy method for conferenceInfo
	 *
	 * @param	string	$info		Requested Array key
	 */
    public function direct($info = null)
    {
        return $this->conferenceInfo($info);
    }
	
	/**
	 * Is the conference live?
	 *
	 * @return	mixed	boolean on false or Zend_Date on true
	 */
    public function isLive()
    {
    	#$test = array('year' => 2011, 'month' => 5, 'day' => 17);
		$date = new Zend_Date();
		
		if ( ( $date->isLater($this->_conference['start'], Zend_Date::ISO_8601)  ) && 
		( $date->isEarlier($this->_conference['end'], Zend_Date::ISO_8601) )  ) {
		    return $date;
		}
		return false;
    }


}
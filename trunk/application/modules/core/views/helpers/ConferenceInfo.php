<?php
/**
 * CORE Conference Manager
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.terena.org/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to webmaster@terena.org so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2011 TERENA (http://www.terena.org)
 * @license    http://www.terena.org/license/new-bsd     New BSD License
 * @revision   $Id$
 */

/**
 * Conference Info View helper
 *
 * @package Core_View_Helper
 */
class Core_View_Helper_ConferenceInfo extends Zend_View_Helper_Abstract
{

	protected $_conference;

	public function conferenceInfo($info = null)
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
	 * Does this conference support Google Calendar?
	 *
	 * @return	boolean
	 */
	public function hasGoogleCalendar()
	{
		if (isset($this->_conference['gcal_url']) &&
			isset($this->_conference['gcal_username']) &&
			isset($this->_conference['gcal_password']) )
			return true;
		else return false;
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

	/**
	 * Has the conference passed?
	 *
	 * @return	mixed	boolean on false or Zend_Date on true
	 */
    public function hasPassed()
    {
		$date = new Zend_Date();

		if ( $date->isLater($this->_conference['end'], Zend_Date::ISO_8601)  )  {
		    return $date;
		}
		return false;
    }

 	/**
	 * Is the submit live?
	 *
	 * @return	mixed	boolean on false or Zend_Date on true
	 */
    public function isSubmitLive()
    {
		$date = new Zend_Date();

		if ( (!isset($this->_conference['submit_start'])) ||
		(!isset($this->_conference['submit_end'])) ) {
			return false;
		}

		if ( ( $date->isLater($this->_conference['submit_start'], Zend_Date::ISO_8601)  ) &&
		( $date->isEarlier($this->_conference['submit_end'], Zend_Date::ISO_8601) )  ) {
		    return $date;
		}
		return false;
    }
}
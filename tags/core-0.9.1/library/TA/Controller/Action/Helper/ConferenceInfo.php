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
 * @revision   $Id: ConferenceInfo.php 598 2011-09-15 20:55:32Z visser $
 */

/**
 * Conference Info Helper
 *
 * @author Christian Gijtenbeek
 * @package TA_Controller
 * @subpackage Helper
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
		$date = new Zend_Date();

		if ( (!isset($this->_conference['start'])) ||
		(!isset($this->_conference['end'])) ) {
			return false;
		}

		if ( ( $date->isLater($this->_conference['start'], Zend_Date::ISO_8601)  ) &&
		( $date->isEarlier($this->_conference['end'], Zend_Date::ISO_8601) )  ) {
		    return $date;
		}
		return false;
    }

    /**
     * Is feedback open? Feedback is open when the feedback codes are sent and the
     * feedback closing date has not passed
	 *
	 * @return	mixed	boolean on false or Zend_Date on true
     */
	public function isFeedbackOpen()
	{
		$eventlogModel = new Core_Model_Eventlog();
		if (false === $feedbackSent = $eventlogModel->getTimestampByType('Core_FeedbackController::mailallAction') ) {
			return false;
		}

		$date = new Zend_Date();

		if ( ( $date->isLater($feedbackSent) ) &&
		( $date->isEarlier($this->_conference['feedback_end'], Zend_Date::ISO_8601) )  ) {
		    return $date;
		}
		return false;
	}

}
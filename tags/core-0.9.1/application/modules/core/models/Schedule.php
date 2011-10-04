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
 * @revision   $Id: Schedule.php 623 2011-09-29 13:25:34Z gijtenbeek $
 */

/** 
 *
 * @package Core_Model
 */
class Core_Model_Schedule extends TA_Model_Acl_Abstract
{

	protected $_schedule = array();

	protected $_days = array();

	protected $_timeslots = array();

	private $_conferenceId;

	/**
	 * Get schedule
	 *
	 * @param		integer		$conferenceId			conference_id
	 * @param		array		$filter		filters to apply to schedule
	 *
	 * foreach($groupedTimeslots as $value => $items)
	 * echo 'Group ' . $value . ' has ' . count($items) . ' ' . (count($items) == 1 ? 'item' : 'items') . "\n";
	 * $date = new Zend_Date();
	 * $format = Zend_Locale_Format::getDateTimeFormat($date->getLocale());
	 *
	 *
	 * @todo cache the output of this method
	 * @return		array		Schedule
	 */
	public function getSchedule($conferenceId = null, $filter = null)
	{

		$schedule = array();

		$groupedTimeslots = array();

		$locationFilter = new StdClass();
		$locationFilter->filters = array('type' => 1);

		$locations = $this->getResource('locations')->getLocations(null, null, $locationFilter);

		// get only timeslots of type 'presentation'
		$timeslots = $this->getResource('timeslots')->getTimeslots(1);

		$subscribedSessions = null;
		if ($filter['personal']) {
			$subscribedSessions = $this->getResource('subscriberssessions')->getSubscriptions();
		}

		$sessions = $this->getResource('sessions')->getAllSessions($subscribedSessions);

		if ($sessions->count() !== 0) {
			// get presentations or speakers based on view filter
			if ($filter['view'] == 'titles') {
				$presentations = $sessions->getAllPresentations();
			} elseif ($filter['view'] == 'speakers') {
				$speakers = $sessions->getAllSpeakers();
			}
		}

		// group timeslots by day
		foreach($timeslots->toArray() as $item) {
			$start = new Zend_Date($item['tstart'],
				    Zend_Date::ISO_8601,
				    Zend_Registry::get('Zend_Locale'));
			$groupedTimeslots[$start->get('dd/MM/yyyy')][$item['timeslot_id']] = $item;
		}

		$this->_timeslots = $groupedTimeslots;

		// array keys of grouped slots represent the days
		$days = $this->_days = array_keys($groupedTimeslots);

		foreach ($days as $day) {

			$timeslots = $groupedTimeslots[$day];
			foreach ($locations['rows'] as $location) {

				foreach ($timeslots as $timeslot) {
					// filter out session array by specific timeslot/location combo
					// the 'use' makes the variables accessible in the anonymous function scope
					// current() gets the first element
					$session = current(array_filter($sessions->toArray(), function($val) use ($timeslot, $location) {
							return ($val['timeslot_id'] == $timeslot['timeslot_id'] &&
									$val['location_id'] == $location->location_id);
							})
					);
					if ($session) {
						$session['loc_abbr'] = $location->abbreviation;
					}
					$schedule[$day][$location->location_id][$timeslot['timeslot_id']] = $session ? $session : null;

					if ($session) {
						if ($filter['view'] == 'speakers') {
							$schedule[$day][$location->location_id][$timeslot['timeslot_id']]['speakers'] =
							(isset($speakers[$session['session_id']]))
								? $speakers[$session['session_id']]
								: null;
						} else {
							$schedule[$day][$location->location_id][$timeslot['timeslot_id']]['presentations'] =
							(isset($presentations[$session['session_id']]))
								? $presentations[$session['session_id']]
								: null;
						}
					}


				}

			}

		}

		if ( ($filter['day'] != 'all') && (!empty($schedule)) ) {
			$scheduleDay[$filter['day']] = $schedule[$filter['day']];
			return $scheduleDay;
		}
		return $schedule;

	}

	/**
	 * Get data needed for streaming page
	 * used by web module
	 */
	public function getStreamData(Zend_Date $date=null)
	{
		// get current and upcoming sessions
		$sessions = $this->getResource('sessionsview')->getSessionsByDate($date);
		$sessionsUpcoming = $this->getResource('sessionsview')->getSessionsAfterDate($date);

		// get current and upcoming speakers
		$speakersCurrent = $sessions->getAllSpeakers();
		$speakersUpcoming = $sessionsUpcoming->getAllSpeakers();

		// get locations
		$locationFilter = new StdClass();
		$locationFilter->filters = array('type' => 1);
		$locations = $this->getResource('locations')->getLocations(null, null, $locationFilter);

		// build roomdata
		$i=0;
		foreach ($locations['rows']->toArray() as $location) {
			$roomdata[$i]['location_file_id'] = $location['file_id'];
			$roomdata[$i]['date'] = $date; // this is usefull for testing dates other than now()
			$roomdata[$i]['roomname'] = $location['name'];
			$roomdata[$i]['session'] = current(
				array_filter($sessions->toArray(), function($val) use ($location) {
					return ($val['location_id'] == $location['location_id']);
				})
			);
			if ($roomdata[$i]['session']) {
				$roomdata[$i]['speakers'] =
					( isset($speakersCurrent[$roomdata[$i]['session']['session_id']]) )
					? $speakersCurrent[$roomdata[$i]['session']['session_id']]
					: null;
			} else {
				$roomdata[$i]['upcoming']['session'] = current(
					array_filter($sessionsUpcoming->toArray(), function($val) use ($location) {
						return ($val['location_id'] == $location['location_id']);
					})
				);
				if ($roomdata[$i]['upcoming']['session']) {
				   $roomdata[$i]['upcoming']['speakers'] =
				   	( $sp = isset($speakersUpcoming[$roomdata[$i]['session']['session_id']]) )
					? $speakersUpcoming[$roomdata[$i]['session']['session_id']]
					: null;
				}
			}
			$i++;
		}

		return $roomdata;
	}

	/**
	 * Get conference days
	 * @return array
	 */
	public function getDays()
	{
		if (!$this->_days) {
			$this->getSchedule();
		}
		return $this->_days;
	}

	/**
	 * Get timeslots
	 * @return array
	 */
	public function getTimeslots()
	{
		return $this->_timeslots;
	}


}
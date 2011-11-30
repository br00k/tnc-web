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
 * @revision   $Id: Abstract.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */

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
	 * Initialize, get conference id
	 *
	 */
	public function init()
	{
		$this->getConferenceId();
	}

	/**
	 * Lazy load conference id
	 *
	 * @return	integer		Conference id
	 */
	public function getConferenceId()
	{
      if (null === $this->_conferenceId) {
      		try {
      			$conference = Zend_Registry::get('conference');
      		} catch (Zend_Exception $e) {
      			return false;
      		}
            $this->setConferenceId($conference['conference_id']);
        }
        return $this->_conferenceId;
	}

	/**
	 * Setter for conference id
	 *
	 * @param	integer		$id
	 * @return	object		TA_Model_Resource_Db_Table_Abstract
	 */
	public function setConferenceId($id)
	{
		$this->_conferenceId = $id;
		return $this;
	}

	/**
	 * Save a row to the database
	 *
	 * @param	array	$data		The data to insert/update
	 * @param	Zend_Db_Table_Row $row optional		The default row to use
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
						#echo "<pre>"; var_dump($data[$column]);
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
	 * @param	object	$o		Observer that implements the iObserver interface
	 * @retunn	void
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
	 * @param	object	$o		Observer that implements the iObserver interface
	 * @retunn	void
	 */
	public function detachObserver(TA_Model_Observer_Interface $o)
	{
		$row = $this->getRowClass();
		$row::detachStaticObserver($o);
	}


}
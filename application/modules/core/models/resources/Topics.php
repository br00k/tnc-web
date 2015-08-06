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
 * @revision   $Id: Topics.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Topics extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'topics';

	protected $_primary = 'topic_id';

	protected $_rowClass = 'Core_Resource_Topic_Item';

	/**
	 * Attach your observers here
	 *
	 */
	public function init() {}

	/**
	 * Gets topic by primary key
	 *
	 * @return object Core_Resource_Topic_Item
	 */
	public function getTopicById($id)
	{
		return $this->find((int) $id)->current();
	}

	/**
	 * Get topics for use in form select
	 *
	 * @param	string	$empty	Empty string to prepend to array
	 * @return	array
	 */
	public function getTopicsForSelect($conferenceId = null, $empty = null)
	{
		$return = $this->getAdapter()->fetchPairs($this->select()
			->from('topics', array('topic_id', 'title'))
			->where('conference_id = ?', $this->getConferenceId())
			->order('lower(title) ASC')
		);

		if ($empty) {
			$return[0] = $empty;
			asort($return);
		}

		return $return;
	}

	/**
	 * Get all topics data
	 *
	 * @param	array	$topics	Array of topic_id's
	 * @return	object	Core_Resource_Session_Set
	 */
	public function getAllTopics($topics)
	{
		$select = $this->select()
					   ->where('conference_id = ?', $this->getConferenceId());
		if ($topics) {
			$select->where('topic_id IN (?)', $topics);
		}

		return $this->fetchAll($select);
	}

	/**
	 *
	 *
	 */
	public function getTopics($paged = null, $order = array())
	{
		$grid = array();
		$grid['cols'] = $this->getGridColumns();
		$grid['primary'] = $this->_primary;

		$select = $this->select();

		if (!empty($order[0])) {
			if ($order[0] == 'updated') {
				$order = $order[0].' '.$order[1];
			} else {
				$order = 'lower('.$order[0].') '.$order[1];
			}
		} else {
			$order = 'lower(title) ASC';
		}
		$select->order($order);

		$select->from($this->info('name'), array_keys($this->getGridColumns()));	   

		if ($paged) {

			$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);

			$paginator = new Zend_Paginator($adapter);
			$paginator->setCurrentPageNumber((int) $paged)
					  ->setItemCountPerPage(20);

			$grid['rows'] = $paginator;
			return $grid;
		}

		$grid['rows'] = $this->fetchAll($select);
		return $grid;

	}

	/**
	 * Convenience method to get grid columns
	 *
	 * @return array
	 */
	private function getGridColumns()
	{
		return array(
			// topic_id is hidden so I don't have to provide a label
			'topic_id' => array('field' => 'topic_id', 'sortable' => true, 'hidden' => true),
			'title' => array('field' => 'title', 'label' => 'Title', 'sortable' => false),
			'description' => array('field' => 'description', 'label' => 'Description', 'sortable' => false)
		);

	}

}
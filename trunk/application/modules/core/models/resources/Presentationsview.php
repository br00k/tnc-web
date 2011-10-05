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
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Presentationsview extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'vw_presentations';

	protected $_primary = 'presentation_id';

	protected $_rowClass = 'Core_Resource_Presentation_Item';

	public function init() {}

	/**
	 * Gets presentation by primary key
	 * @return object Core_Resource_Presentation_Item
	 */
	public function getPresentationById($id)
	{
		return $this->find( (int)$id )->current();
	}


	public function getPresentations($paged=null, $order=array(), $filter=null, $unique=false)
	{

		$grid = array();
		$grid['cols'] = $this->getGridColumns();
		$grid['primary'] = $this->_primary;

		if (!$filter) {
			$filter = new stdClass();
			$filter->filters = new stdClass();
		}

		$filter->filters->conference_id = $this->getConferenceId();
		$select = $this->select();

		if (!empty($order[0])) {
			if ($order[0] == 'inserted') {
				$order = $order[0] .' '. $order[1];
			} else {
				$order = 'lower('.$order[0].') '.$order[1];
			}
		} else {
			$order = 'lower(presentation_title) ASC';
		}
		$select->order($order);

		$select->from( $this->info('name'), array_keys($this->getGridColumns()) );

		// apply filters to grid
		if ($filter->filters) {
			foreach ($filter->filters as $field => $value) {
			    if (is_array($value)) {
			        $select->where( $field.' IN (?)', $value);
			    } else {
			        $select->where( $field.' = ?', $value);
			    }
			}
		}

		if ($paged) {

			$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);

			$paginator = new Zend_Paginator($adapter);
			$paginator->setCurrentPageNumber( (int)$paged )
					  ->setItemCountPerPage(20);

			$grid['rows'] = $paginator;
			return $grid;
		}

		if ($unique) {
			$grid['rows'] = $this->getAdapter()->fetchAssoc($select);
		} else {
			$grid['rows'] = $this->fetchAll($select);
		}
		
		if (empty($grid['rows'])) {
			return false;
		}
		
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
			// presentation_id is hidden so I don't have to provide a label
			'presentation_id' => array('field' => 'presentation_id', 'sortable' => true, 'hidden' => true),
			'session_id' => array('field' => 'session_id', 'sortable' => false, 'hidden' => true),
			'presentation_title' => array('field' => 'presentation_title', 'label' => 'Title', 'sortable' => true),
			'email' => array('field' => 'email', 'label' => 'User', 'sortable' => true, 'resource' => 'session', 'privilege' => 'save'),
			'session_title' => array('field' => 'session_title', 'label' => 'Session', 'sortable' => true )
		);

	}
}
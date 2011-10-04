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
class Core_Resource_Reviews extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'reviews';

	protected $_primary = 'review_id';

	protected $_rowClass = 'Core_Resource_Review_Item';

	public function init() 
	{	
		$config = Zend_Registry::get('config');

		if ($config->core->observer->review == 1) {
			$this->attachObserver(new Core_Model_Observer_Review());
		}
	}

	/**
	 * Gets conference by primary key
	 * @return object Core_Resource_Conference_Item
	 */
	public function getReviewById($id)
	{
		return $this->find( (int)$id )->current();
	}

	public function getReviews($paged=null, $order=array(), $filter=null)
	{
		$grid = array();
		$grid['cols'] = $this->getGridColumns();
		$grid['primary'] = $this->_primary;

		$select = $this->select();

		if (!empty($order[0])) {
			$order = $order[0].' '.$order[1];
		} else {
			$order = 'inserted ASC';
		}
		$select->order($order);

		$select->from( 'reviews', array_keys($this->getGridColumns()) );
		if ($filter) {
			$select->where( 'submission_id = ?', (int) $filter);
		}

		if ($paged) {

			$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);

			$paginator = new Zend_Paginator($adapter);
			$paginator->setCurrentPageNumber( (int)$paged )
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
			// conference_id is hidden so I don't have to provide a label
			'review_id' => array('field' => 'review_id', 'sortable' => true, 'hidden' => true),
			'submission_id' => array('field' => 'submission_id', 'hidden' => true),
			'inserted' => array('field' => 'inserted', 'label' => 'Date', 'sortable' => true, 'modifier' => 'formatDate'),
			'suitability_conf' => array('field' => 'suitability_conf', 'label' => 'Suitability', 'sortable' => true),
			'rating' => array('field' => 'rating', 'label' => 'Rating', 'sortable' => true),
			'self_assessment' => array('field' => 'self_assessment', 'label' => 'Self Assessment', 'sortable' => true),
			'quality' => array('field' => 'quality', 'label' => 'Quality', 'sortable' => true),
			'importance' => array('field' => 'importance', 'label' => 'Importance', 'sortable' => true),
			'comments_presentation' => array('field' => 'comments_presentation', 'label' => 'Comments presentation', 'sortable' => true),
			'comments_pc' => array('field' => 'comments_pc', 'label' => 'Comments PC', 'sortable' => true),
			'comments_authors' => array('field' => 'comments_authors', 'label' => 'Comments authors', 'sortable' => true),
			'user_id' => array('field' => 'user_id', 'label' => 'Reviewer', 'sortable' => true)
		);

	}

}
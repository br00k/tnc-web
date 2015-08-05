<?php
class Core_Resource_Eventsview extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'vw_events';

	protected $_primary = 'event_id';

	protected $_rowClass = 'Core_Resource_Event_Item';
	
	protected $_rowsetClass = 'Core_Resource_Event_Set';

	public function init() {}
	
	/**
	 * Gets event by primary key
	 * @return object Core_Resource_Event_Item
	 */
	public function getEventById($id)
	{
		return $this->find( (int)$id )->current();
	}
	
	public function getEvents($paged=null, $order=array())
	{
		$grid = array();
		$grid['cols'] = $this->getGridColumns();
		$grid['primary'] = $this->_primary;

		$select = $this->select();

		if (!empty($order[0])) {
			$order = $order[0] .' '.$order[1];
		} else {
			$order = 'lower(title) ASC';
		}
		$select->order($order);

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
	 * @todo in php5.3 I can add lambda's for modifiers
	 */
	private function getGridColumns()
	{
		return array(
			// conference_id is hidden so I don't have to provide a label
			'event_id' => array('field' => 'conference_id', 'sortable' => true, 'hidden' => true),
			'title' => array('field' => 'title', 'label' => 'Title', 'sortable' => true),
			'tstart' => array('field' => 'tstart', 'label' => 'start', 'sortable' => false),
			'tend' => array('field' => 'tend', 'label' => 'end', 'sortable' => false),
			'category' => array('field' => 'category', 'label' => 'category', 'sortable' => false),
			'persons' => array('field' => 'persons', 'label' => 'persons', 'sortable' => false),
			'category_id' => array('field' => 'category_id', 'label' => 'category_id', 'sortable' => false)				
		);
	}

}
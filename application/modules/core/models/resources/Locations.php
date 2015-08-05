<?php
class Core_Resource_Locations extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'locations';

	protected $_primary = 'location_id';

	protected $_rowClass = 'Core_Resource_Location_Item';

	public function init() {
		parent::init();
	}

	/**
	 * Gets location by primary key
	 * @return object Core_Resource_Location_Item
	 */
	public function getLocationById($id)
	{
		return $this->find( (int)$id )->current();
	}

	/**
	 * 
	 * @param	$type	boolean	Show only rooms (for schedule) or all locations?
	 */
	public function getLocationsForSelect($type = null)
	{
		$select = $this->select()
			->where('conference_id = ?', $this->getConferenceId())
			->order('lower(name) ASC');
		
		if ($type) {
			$select->where('type = ?', (int) $type);
		}	
		
		return $this->getAdapter()->fetchPairs($select);
	}
	
	/**
	 *
	 * @param	mixed	$paged
	 * @param	array	$order
	 * @param	object	$filter		Filter object, should contain 'filter' property
	 * @return	array
	 *
	 */
	public function getLocations($paged=null, $order=array(), $filter=null)
	{
		$grid = array();
		$grid['cols'] = $this->getGridColumns();
		$grid['primary'] = $this->_primary;

		$select = $this->select();

		if (!empty($order[0])) {
			$order = 'lower('.$order[0].') '.$order[1];
		} else {
			$order = 'lower(name) ASC';
		}
		$select->order($order);

		$select->from( $this->info('name'), array_keys($this->getGridColumns()) )
			   ->where( 'conference_id = ?', $this->getConferenceId());

		if ($filter) {
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
	 * @todo in php5.3 I can add lambda's for modifiers
	 */
	private function getGridColumns()
	{
		return array(
			// location_id is hidden so I don't have to provide a label
			'location_id' => array('field' => 'location_id', 'sortable' => true, 'hidden' => true),
			'name' => array('field' => 'name', 'label' => 'Name', 'sortable' => true),
			'abbreviation' => array('field' => 'abbreviation', 'label' => 'Abbreviation', 'sortable' => true),
			'capacity' => array('field' => 'capacity', 'label' => 'Capacity', 'sortable' => true),			
			'file_id' => array('field' => 'file_id', 'hidden' => true)
		);

	}

}
<?php
class Core_Resource_Posters extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'posters';

	protected $_primary = 'poster_id';

	protected $_rowClass = 'Core_Resource_Poster_Item';

	public function init() {}

	/**
	 * Gets deadline by primary key
	 * @return object Zend_Db_Table_Row
	 */
	public function getPosterById($id)
	{
		return $this->find( (int)$id )->current();
	}

	public function getPosters($paged=null, $order=array())
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
			'poster_id' => array('field' => 'conference_id', 'sortable' => true, 'hidden' => true),
			'title' => array('field' => 'title', 'label' => 'Title', 'sortable' => true),
			'description' => array('field' => 'description', 'label' => 'Description', 'hidden'=> true),
			'title' => array('field' => 'title', 'label' => 'Title', 'sortable' => true),
			'inserted' => array('field' => 'inserted', 'label' => 'Inserted', 'hidden'=> true),
			'file_id' => array('field' => 'file_id', 'label' => 'file', 'sortable' => true, 'hidden' => true),
		);
	}

}
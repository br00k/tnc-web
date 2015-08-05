<?php
class Core_Resource_Presentations extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'presentations';

	protected $_primary = 'presentation_id';

	protected $_rowClass = 'Core_Resource_Presentation_Item';

	/**
	 * Void
	 *
	 */
	public function init() {}

	/**
	 * Gets presentation by primary key
	 * @return object Core_Resource_Presentation_Item
	 */
	public function getPresentationById($id)
	{
		return $this->find( (int)$id )->current();
	}

	/**
	 * Get presentations for use in form select
	 *
	 * @param	string	$empty	Empty string to prepend to array
	 * @return	array
	 */
	public function getPresentationsForSelect($conferenceId, $empty = null)
	{
		$return = array();

		$return = $this->getAdapter()->fetchPairs($this->select()
			->from('presentations', array('presentation_id', 'title'))
			->order('lower(title) ASC')
		);

		if ($empty) {
			$return[0] = $empty;
			asort($return);
		}

		return $return;
	}

	/**
	 * Link submissions to presentation
	 *
	 * @param Core_Resource_Submission_Set $submissions RowSet of submissionsview
	 * @return Zend_Db_Statement_Pdo on success, false on failure
	 */
	public function linkSubmissions(Core_Resource_Submission_Set $submissions)
	{
		if ($submissions->count() === 0) {
			return false;
		}

		$db = $this->getAdapter();
		$conferenceId = $this->getConferenceId();

		foreach ($submissions as $submission) {
			$values[] = "("
				.$conferenceId.','
				.$submission->submission_id.','
				.$db->quote($submission->title)
				.")";
		}
		$values = implode(',', $values);

		// Transaction takes too much memory!
		#$db->beginTransaction();

		try {
			#$this->delete(
			#	$db->quoteInto('conference_id = ?', $conferenceId)
			#);
			$query = "INSERT INTO " . $this->_name . "(conference_id, submission_id, title) VALUES ".$values;
			$query = $db->query($query);

			// get newly inserted presentations (inserted in last 10 seconds)
			$presSess = "select p.presentation_id, ss.session_id from presentations p left join submission_status ss on (p.submission_id=ss.submission_id) where inserted > now() - INTERVAL '10 seconds'";
			$presSess = $db->query($presSess)->fetchAll();

			foreach ($presSess as $ps) {
				$insertValues[] = "("
				.$ps['presentation_id'].','
				.$ps['session_id'].','
				.'999'
				.")";
			}

			// insert session presentation links
			$db->query(
				"insert into sessions_presentations (presentation_id, session_id, displayorder) values "
				.implode(',', $insertValues)
			);

			#$db->commit();

			return $query;
		} catch (Exception $e) {
			$db->rollBack();
			throw new TA_Model_Exception($e->getMessage());
		}
	}

	/**
	 * List all presentations
	 *
	 */
	public function getPresentations($paged=null, $order=array())
	{
		$grid = array();
		$grid['cols'] = $this->getGridColumns();
		$grid['primary'] = $this->_primary;

		$select = $this->select();

		if (!empty($order[0])) {
			$order = 'lower('.$order[0].') '.$order[1];
		} else {
			$order = 'lower(title) ASC';
		}
		$select->order($order);

		$select->from( 'presentations', array_keys($this->getGridColumns()) )
			   ->where( 'conference_id = ?', $this->getConferenceId());

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
			// presentation_id is hidden so I don't have to provide a label
			'presentation_id' => array('field' => 'presentation_id', 'sortable' => true, 'hidden' => true),
			'title' => array('field' => 'title', 'label' => 'Title', 'sortable' => true),
			'inserted' => array('field' => 'inserted', 'label' => 'Inserted', 'sortable' => true, 'modifier' => 'formatDate' )
		);

	}

}
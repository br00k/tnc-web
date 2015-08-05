<?php
class Core_Resource_Submissions extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'submissions';

	protected $_primary = 'submission_id';

	protected $_rowClass = 'Core_Resource_Submission_Item';

	protected $_rowsetClass = 'Core_Resource_Submission_Set';

	public function init() {}

	/**
	 * Gets submission by primary key
	 * @return object Core_Resource_Submission_Item
	 */
	public function getSubmissionById($id)
	{
		return $this->find( (int)$id )->current();
	}

	/**
	 * @todo: is this used anywhere??
	 *
	 */
	public function mailSubmissions($id)
	{
		$submissions = $this->getSubmissions(null, null, (int) $id);
		#Zend_Debug::dump($submissions);exit();
	}

	/**
	 * Get form value from config for accepted 'status'
	 *
	 * @return int
	 */
	private function _getAcceptedValue()
	{
		$formConfig = new Zend_Config(require APPLICATION_PATH.'/configs/formdefaults.php');
		Zend_Registry::set('formconfig', $formConfig);

		$status = Zend_Registry::get('formconfig')
								->formdefaults
								->submit
								->status
								->toArray();
		foreach ($status as $k=>$v) {
			if ($v === 'yes') {
				return (int) $k;
			}
		}
	}

	/**
	 * Get all accepted submissions belonging to a conference
	 *
	 * @param integer $conferenceId conference_id
	 * @param string $empty String containing the empty value to display
	 */
	public function getSubmissionsForSelect($conferenceId = null, $empty = null)
	{
		$return = array();

		if ($empty) {
			$return[0] = $empty;
		}

		$identity = Zend_Auth::getInstance()->getIdentity();

		$query = 'select st.submission_id, s.title from submission_status st
		left join submissions s ON s.submission_id = st.submission_id
		where st.status = :status AND s.conference_id = :conference_id';

		if (!$identity->isAdmin()) {
			// if user is not admin, only show their own submissions
			$mySubmissions = implode(",", $identity->getMySubmissions());
			if (!empty($mySubmissions)) {
				$query .= ' and st.submission_id IN ('.$mySubmissions.')';
			} else {
				return array();
			}
		}

		$submissions = $this->getAdapter()->query(
			$query,
			array(
				'status' => $this->_getAcceptedValue(),
				'conference_id' => $this->getConferenceId()
			)
		);
		foreach ($submissions as $submission) {
			$return[$submission['submission_id']] = $submission['title'];
		}
		return $return;
	}

	/**
	 *
	 *
	 */
	public function getSubmissions($paged=null, $order=array(), $filter=null)
	{
		$grid = array();
		$grid['cols'] = $this->getGridColumns();
		$grid['primary'] = $this->_primary;

		$select = $this->select();

		if (!empty($order[0])) {
			$order = $order[0].' '.$order[1];
		} else {
			$order = 'lower(title) ASC';
		}
		$select->order($order)
			   ->setIntegrityCheck(false);

		$select->from( 'vw_submissions', array_keys($this->getGridColumns()) );
		if ($filter) {
			$select->where( 'conference_id = ?', (int) $filter);
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

	public function saveUserSubmission(array $data)
	{
		$userReference = new Core_Resource_UsersSubmissions();
		$userReference->insert($data);
	}

	/**
	 * Convenience method to get grid columns
	 *
	 * @return array
	 */
	private function getGridColumns()
	{
		return array(
			'submission_id' => array('field' => 'submission_id', 'sortable' => true, 'hidden' => true),
			'title' => array('field' => 'title', 'label' => 'Title', 'sortable' => true),
			'date' => array('field' => 'date', 'label' => 'Received', 'sortable' => true, 'modifier' => 'formatDate'),
			'organisation' => array('field' => 'organisation', 'label' => 'Organisation', 'sortable' => true),
			'fname' => array('field' => 'fname', 'label' => 'Fname', 'sortable' => true),
			'lname' => array('field' => 'lname', 'label' => 'Lname', 'sortable' => true),
			'email' => array('field' => 'email', 'label' => 'Email', 'sortable' => true),
			'file_id' => array('field' => 'file_id', 'label' => 'File', 'sortable' => true),
			'status' => array('field' => 'status', 'label' => 'Status', 'sortable' => true),
			'session_title' => array('field' => 'session_title', 'label' => 'Session', 'sortable' => true),
			'review_first' => array('field' => 'review_first', 'label' => 'Review first', 'sortable' => true),
			'review_last' => array('field' => 'review_last', 'label' => 'Review last', 'sortable' => true),
			'conference_id' => array('field' => 'conference_id', 'label' => 'Conference id', 'sortable' => true),
			'review_count' => array('field' => 'review_count', 'label' => 'Reviews', 'sortable' => true)
		);

	}

}
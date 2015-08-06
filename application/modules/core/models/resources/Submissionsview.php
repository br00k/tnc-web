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
	 * @revision   $Id: Submissionsview.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
	 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Submissionsview extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'vw_submissions';

	protected $_primary = 'submission_id';

	protected $_rowsetClass = 'Core_Resource_Submission_Set';

	public function init() {}

	/**
	 * Gets submission by primary key
	 * @return object Core_Resource_Submission_Item
	 */
	public function getSubmissionById($id)
	{
		return $this->find((int) $id)->current();
	}

	/**
	 * Get submissions for email action of submit
	 *
	 */
	public function getSubmissionsForMail($statusId)
	{
		$query = 'select u.email, u.fname, u.lname, u.invite, st.session_id, st.status, st.submission_id, s.title
				from submission_status st
				left join submissions s on (st.submission_id = s.submission_id)
				left join users_submissions us on (us.submission_id = st.submission_id)
				left join users u on (u.user_id = us.user_id) where st.status='
				.(int) $statusId.' and s.conference_id='.$this->getConferenceId();
		return $this->getAdapter()->fetchAll($query);
	}

	/**
	 * Get all submissions that are accepted
	 *
	 * @param	array	$values		Form values
	 * @return	Zend_Db_Table_Rowset
	 */
	public function getAcceptedSubmissions($values)
	{
		$start = new Zend_Date($values['submit_start']);
		$end = new Zend_Date($values['submit_end']);

		return $this->fetchAll($this->select()
			->where('conference_id = ?', $this->getConferenceId())
			->where('status = ?', $this->_getAcceptedValue())
			->where('submission_insert > ?', $start->get(Zend_Date::ISO_8601))
			->where('submission_insert < ?', $end->get(Zend_Date::ISO_8601))
		);
	}

	/**
	 * @param	object	$filter
	 * @param	boolean	$object		Return object
	 * @param	boolean $submissionId	Include submission id
	 * @return	mixed
	 */
	public function getFileIds($filter, $object=false, $submissionId=false)
	{
		$select = $this->select()
			   		   ->from( $this->info('name'), array('file_id', 'title'));
			   		   
		if ($submissionId) {
			$select = $this->select()
			   		   	   ->from( $this->info('name'), array('file_id', 'submission_id'));			
		}

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
		if ($object) {
			return $this->fetchAll($select);
		}
		if ($submissionId) {
			return $this->getAdapter()->fetchPairs($select);
		}
		return $this->getAdapter()->fetchCol($select);
	}

	/**
	 *
	 * @param	mixed	$paged
	 * @param	array	$order
	 * @param	object	$filter		Filter object, should contain 'filter' property
	 * @return	array
	 *
	 */
	public function getSubmissions($paged = null, $order = array(), $filter = null)
	{
		$grid = array();
		$grid['cols'] = $this->getGridColumns();
		$grid['primary'] = $this->_primary;

		$filter->filters->conference_id = $this->getConferenceId();

		$select = $this->select();

		if (!empty($order[0])) {
			$order = $order[0] . ' ' . $order[1];
		} else {
			$order = 'lower(title) ASC';
		}
		$select->order($order)
			   ->from( $this->info('name'), array_keys($this->getGridColumns()) );

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
			$paginator->setCurrentPageNumber((int) $paged)
					  ->setItemCountPerPage(20);

			$grid['rows'] = $paginator;
			#Zend_Debug::dump($grid['rows']->getItemsByPage(1));exit();
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
	 * Convenience method to get grid columns
	 *
	 * @return array
	 */
	private function getGridColumns()
	{
		return array(
			'submission_id' => array('field' => 'submission_id', 'sortable' => true, 'hidden' => true),
			'session_id' => array('field' => 'session_id', 'sortable' => true, 'hidden' => true),
			'title' => array('field' => 'title', 'label' => 'Title', 'sortable' => true),
			'submission_type' => array('field' => 'submission_type', 'label' => 'Type', 'sortable' => false),
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
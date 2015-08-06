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
	 * @revision   $Id: Presentations.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
	 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
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
		return $this->find((int) $id)->current();
	}

	/**
	 * Get presentations for use in form select
	 *
	 * @param	string	$empty	Empty string to prepend to array
	 * @return	array
	 */
	public function getPresentationsForSelect($empty = null)
	{
		$return = array();

		$return = $this->getAdapter()->fetchPairs($this->select()
			->from('presentations', array('presentation_id', 'title'))
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
	 * Link submissions to presentation
	 *
	 * @param	Core_Resource_Submission_Set $submissions RowSet of submissionsview
	 * @param	Zend_Config		$config		Form values passed as config
	 * @return	Zend_Db_Statement_Pdo on success, false on failure
	 */
	public function linkSubmissions(Core_Resource_Submission_Set $submissions, Zend_Config $config)
	{
		if ($submissions->count() === 0) {
			return false;
		}

		$db = $this->getAdapter();
		$conferenceId = $this->getConferenceId();

		foreach ($submissions as $submission) {
			$submissionIds[] = $submission->submission_id;
			$values[] = "("
				.$conferenceId.','
				.$submission->submission_id.','
				.$db->quote($submission->abstract).','
				.$db->quote($submission->title)
				.")";
		}
		$values = implode(',', $values);
		$submissionIds = implode(',', $submissionIds);

		try {
			if ($config->_overwrite) {
				$this->delete(
				   $db->quoteInto('conference_id = ?', $conferenceId)
				);
			}

			$query = "INSERT INTO ".$this->_name."(conference_id, submission_id, abstract, title) VALUES ".$values;
			$query = $db->query($query);

			// get newly inserted presentations
			$query = "select p.presentation_id, s.user_id, s.session_id, p.submission_id, s.file_id from presentations p ".
			"left join vw_submissions s on (p.submission_id = s.submission_id) where p.submission_id IN (".$submissionIds.")";
			$collection = $db->fetchAll($query);

			foreach ($collection as $value) {
				$valuesPu[$value['presentation_id']] = $value['user_id'];

				$valuesSp[] = "("
					.$value['session_id'].","
					.$value['presentation_id'].","
					."999)";

				$valuesPf[] = "("
					.$value['file_id'].","
					.$value['presentation_id'].")";
					
				$valuesF[] = $value['file_id'];
			}

			// insert session presentation links
			if ($config->link_sessions) {
				$db->query(
					"insert into sessions_presentations (session_id, presentation_id, displayorder) values ".
					implode(',', $valuesSp)
				);
			}

			// insert file presentation links
			if ($config->link_files) {
				$db->query(
					"insert into presentations_files (file_id, presentation_id) values ".
					implode(',', $valuesPf)
				);
				// update filetype to paper
				$db->update('files', array('filetype' => 4), array('file_id IN (?)' => $valuesF));
			}

			return $valuesPu;
		} catch (Exception $e) {
			throw new TA_Model_Exception($e->getMessage());
		}
	}

	/**
	 * List all presentations
	 *
	 */
	public function getPresentations($paged = null, $order = array())
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

		$select->from('presentations', array_keys($this->getGridColumns()))
			   ->where('conference_id = ?', $this->getConferenceId());

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
			// presentation_id is hidden so I don't have to provide a label
			'presentation_id' => array('field' => 'presentation_id', 'sortable' => true, 'hidden' => true),
			'title' => array('field' => 'title', 'label' => 'Title', 'sortable' => true),
			'inserted' => array('field' => 'inserted', 'label' => 'Inserted', 'sortable' => true, 'modifier' => 'formatDate')
		);

	}

}
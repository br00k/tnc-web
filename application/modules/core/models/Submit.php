<?php

class Core_Model_Submit extends TA_Model_Acl_Abstract
{

	/**
	 * Get submission by id
	 * @param		integer		$id		Submission id
	 * @return		Core_Resource_Submission_Item
	 */
	public function getSubmissionById($id)
	{
		$row = $this->getResource('submissions')->getSubmissionById( (int) $id );
    	if ($row === null) {
    		throw new TA_Model_Exception('id not found');
    	}
    	return $row;
	}

	/**
	 * Get submission status by submission_id
	 *
	 * @param	integer		$id		submission_id
	 * @return	array
	 */
	public function getStatusBySubmissionId($id)
	{
		$return = array('submission_id'=>$id);
		$row = $this->getResource('submissionstatus')->getStatusBySubmissionId( (int) $id );
    	if ($row === null) {
    		return $return;
    	}
    	return $row->toArray();
	}

	/**
	 * Save submission status
	 *
	 * @param	array	$post	Post values
	 * @return	boolean
	 */
	public function saveStatus(array $post)
	{
		if (!$this->checkAcl('statusSave')) {
			throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		$form = $this->getForm('submitStatus');
		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		// get filtered values
		$values = $form->getValues();

		$resource = $this->getResource('submissionstatus');

		// @fix: CORE-54 Check if row exists, if it does pass the row as a second parameter
		// to saveRow() - this will then update instead or insert
		$submitStatus = ($resource->getStatusBySubmissionId($values['submission_id']))
			? $resource->getStatusBySubmissionId($values['submission_id'])
			: null;

		return $resource->saveRow($values, $submitStatus);
	}

	/**
	 * Get a list of submissions.
	 * If a user is allowed to review submissions, only the submissions they should
	 * review are shown
	 *
	 * @param		integer		$page	Page number to show
	 * @param		array		$order	Array with keys 'field' and 'direction'
	 * @return		array		Grid array with keys 'cols', 'primary', 'rows'
	 */
	public function getSubmissions($paged = null, $order = array(), $session = null)
	{
		if (!$this->checkAcl('list')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		return $this->getResource('submissionsview')->getSubmissions($paged, $order, $session);
	}

	/**
	 * Build zip archive of submission files
	 *
	 * @return	string	filename
	 */
	public function getArchiveBySubmissionIds($filter, $format='zip')
	{
		if (!$this->checkAcl('list')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		$files = $this->getResource('filesview')->getFilesByIds(
			$this->getResource('submissionsview')->getFileIds($filter)
		);
		$files = $files->getNormalizedFiles();
		
		$zip = new ZipArchive();
		$conference = Zend_Registry::get('conference');
		$zipFilename = $conference['abbreviation'].
					   '_userid_'.
					   Zend_Auth::getInstance()->getIdentity()->user_id.
					   '_'.date("Y-m-d_Gi\ms\s").'.zip';
		if ($zip->open(Zend_Registry::get('config')->directories->uploads.$zipFilename, ZIPARCHIVE::CREATE) === true) {
			foreach ($files as $fullFilePath => $renameTo) {
				$zip->addFile($fullFilePath, $renameTo);
			}
			$zip->close();
		} else {
			throw new Exception('unable to create archive');
		}
		
		return $zipFilename;
	}

	/**
	 * Get all submission data by submission id
	 * @param		integer		$id		Submission id
	 * @return		Core_Resource_Submissionview_Item
	 */
	public function getAllSubmissionDataById($id)
	{
		$row = $this->getResource('submissionsview')->getSubmissionById( (int) $id );
    	if ($row === null) {
    		throw new TA_Model_Exception('id not found');
    	}
    	return $row;
	}

	/**
	 * Get all submission data by review item Object
	 * @param		Core_Resource_Review_Item		$review		Review object
	 * @return		Core_Resource_Submissionview_Item
	 */
	public function getAllSubmissionDataByReview(Core_Resource_Review_Item $review)
	{
		return $this->getAllSubmissionDataById($review->submission_id);
	}

	/**
	 * Get a list of submissions.
	 * @param		integer		$conferenceId	Conference Id
	 * @param		string		$empty			String containing the empty value to display
	 * @return		array
	 */
	public function getSubmissionsForSelect($conferenceId = null, $empty = null)
	{
		return $this->getResource('submissions')->getSubmissionsForSelect($conferenceId, $empty);
	}

	/**
	 * Get list of submitters of accepted/rejected papers
	 *
	 * @param	integer		$statusId		Status (1=accepted, 2=rejected)
	 * @return	array		contains: submission/reviews/session
	 */
	public function getSubmissionsForMail($statusId)
	{
		if (!$this->checkAcl('mail')) {
			throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		$list = array();

		// get all submissions with defined status
		$submissions = $this->getResource('submissionsview')->getSubmissionsForMail($statusId);
		if (empty($submissions)) {
			return false;
		}
		// get reviews of the submissions
		$reviews = $this->getResource('reviewsview')->getReviewsByIds($submissions);

		// get proposed sessions of the submissions
		if ($statusId == 1) {
			if (false == $sessions = $this->getResource('sessionsview')->getSessionsByIds($submissions) ) {
				throw new Exception('Found accepted submission that does not belong to a session, please fix!');
			}
			$chairs = $sessions->getChairs();
		}

		foreach ($submissions as $submission) {
			$list[$submission['submission_id']] = $submission;

			// only reviews of this submission
			$review = array_filter($reviews->toArray(), function($val) use($submission) {
		        return ($val['submission_id'] == $submission['submission_id']);
			});
			$list[$submission['submission_id']]['reviews'] = $review ? $review : null;

			if ($statusId == 1) {
				// get session that this submission belongs to
				// use current because a submission can only belong to one session
				$session = current(array_filter($sessions->toArray(), function($val) use($submission) {
		    	    return ($val['session_id'] == $submission['session_id']);
				}));
				$chair = array_filter($chairs, function($val) use($submission) {
		    	    return ($val['session_id'] == $submission['session_id']);
				});
				$list[$submission['submission_id']]['session'] = $session ? $session : null;
				$list[$submission['submission_id']]['session']['chair'] = $chair ? $chair : null;
			}
		}

		return $list;

	}

	/**
	 * Get all submissions belonging to a conference that are accepted
	 *
	 * @param integer $conferenceId conference_id
	 * @return Zend_Db_Table_Rowset
	 */
	public function getAcceptedSubmissions($conferenceId = null)
	{
		return $this->getResource('submissionsview')->getAcceptedSubmissions($conferenceId);
	}


	public function deleteReviewer($id = null)
	{
		if (!$this->checkAcl('reviewerDelete')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		if (!$id) {
			return false;
		}
		$id = (int) $id;

		return $this->getResource('reviewerssubmissions')->getItemById($id)->delete();
	}

	public function saveReviewers(array $post)
	{
		if (!$this->checkAcl('reviewerSave')) {
			throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		$form = $this->getForm('submitUser');
		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		// get filtered values
		$values = $form->getValues();

		$resource = $this->getResource('reviewerssubmissions');

		// if user submission link does not already exist, save link
		if (!$resource->getItemByValues($values)) {
			// give user reviewer role
			$this->getResource('users')->addUserRole($values['user_id'], 'reviewer');
			return $resource->saveRow($values);
		} else {
			return false;
		}
	}

	/**
	 * Remove submission from resource
	 * @param		integer		$id		Id of record to delete
	 * @return		boolean
	 */
	public function delete($id = null)
	{
		if (!$this->checkAcl('delete')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		if (!$id) {
			return false;
		}
		$id = (int) $id;

		return $this->getSubmissionById($id)->delete();
	}

	/**
	 * Save submission without file upload
	 *
	 * @param		array	$post	Post request
	 * @param		string	$action	The subform part to validate against
	 */
	public function saveSubmissionOnly(array $post, $action = null)
	{
        // get different form based on action parameter
		$formName = ($action) ? 'submit' . ucfirst($action) : 'submit';
		$form = $this->getForm($formName);

		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		$values = $form->getSubForm('submission')->getValues(true);

		$submit = array_key_exists('submission_id', $values) ?
			$this->getResource('submissions')
				 ->getSubmissionById($values['submission_id']) : null;

		// persist submission
		$submissionId = $this->getResource('submissions')->saveRow($values, $submit);
		return $submissionId;
	}

	/**
	 * Save submission to resource
	 *
	 * @param		array	$post	Post request
	 * @param		string	$action	The subform part to validate against
	 * @return		mixed	The primary key of the inserted/updated record or resource error message
	 */
	public function saveSubmission(array $post, $action = null)
	{
		if (!$this->checkAcl('save')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

        // get different form based on action parameter
		$formName = ($action) ? 'submit' . ucfirst($action) : 'submit';
		$form = $this->getForm($formName);

		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		// no file upload, so only save submission
		if ( !$form->submission->file->isUploaded() ) {
			return $this->saveSubmissionOnly($post, $action);
		}

		// save file to filesystem
		try {
			$fileInfo = array();
			$adapter = $form->submission->file->getTransferAdapter();
		    $hash = $adapter->getHash('sha1');

		    $form->submission->file->addFilter('rename', array(
		        'target' => Zend_Registry::get('config')->directories->uploads.$hash,
		        'overwrite' => true // @todo: set to FALSE when debugging is over!
		    ));

		    $origName = $adapter->getFileName();
		    $adapter->receive();
			$fileInfo = $adapter->getFileInfo();
			$fileInfo['file']['_filename_original'] = $origName;
			$fileInfo['file']['_filehash'] = $hash;
			// @todo: add filetype 'submissions' to this
			$fileInfo['file']['_filetype'] = 1;
		} catch (Zend_File_Transfer_Exception $e) {
			$e->getMessage();
		}

		$db = $this->getResource('files')->getAdapter();
		$db->beginTransaction();

		try {
			// persist file
			$fileId = $this->getResource('files')->saveRow($fileInfo);

			// get submission subform values
			$submissionValues = $form->getSubForm('submission')->getValues(true);

			$submit = array_key_exists('submission_id', $submissionValues) ?
				$this->getResource('submissions')
					 ->getSubmissionById($submissionValues['submission_id']) : null;

			$submissionValues['file_id'] = $fileId;

			// persist submission
			$submissionId = $this->getResource('submissions')->saveRow($submissionValues, $submit);

			// persist user_submission only on 'new' action
			if ($action != 'edit') {
				$userSubmission = $this->getResource('submissions')->saveUserSubmission(array(
					'user_id' => Zend_Auth::getInstance()->getIdentity()->user_id,
					'submission_id' => $submissionId
				));
			}

			$db->commit();
			return $userSubmission;
		} catch (Exception $e) {
			$db->rollBack();
			throw new TA_Model_Exception($e->getMessage());
		}
	}


}
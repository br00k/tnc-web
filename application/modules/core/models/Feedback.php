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
 * @revision   $Id: Feedback.php 103 2013-04-01 15:13:41Z gijtenbeek@terena.org $
 */

/**
 *
 * @package Core_Model
 * @author Christian Gijtenbeek
 */
class Core_Model_Feedback extends TA_Model_Acl_Abstract
{

	protected $_feedback_id;

	protected $_post;

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function getResourceId()
	{
		return 'Feedback';
	}

	/**
	 * Initialize feedback, sets feedback_id from UUID value
	 * This value can be provided via the url or a cookie and
	 * will be stored in a session
	 *
	 * If no valid feedback code is found in the request string, the cookie
	 * or in the session (also a cookie) then return false
	 *
	 * @return	integer		feeback_id
	 */
	private function _init()
	{
		$request = Zend_Controller_Front::getInstance()->getRequest();

		$conference = Zend_Registry::get('conference');
		$sessionNs = $conference['abbreviation'].'_feedback';

		// check if feedback deadline has passed
		if (isset($conference['feedback_end'])) {
			if (Zend_Date::now()->isLater($conference['feedback_end'])) {
				return false;
			}
		}

		// check if session is set
		if (Zend_Session::namespaceIsset($sessionNs)) {
			$session = new Zend_Session_Namespace($sessionNs, true);
			return $this->_feedback_id = $session->feedback_id;
		}

		// for uuid parameter, first try Request value, if not available use Cookie value
		$uuid = $request->getParam('uuid', $request->getCookie('feedback_code'));

		// use parameter to set session and cookie
		if ($uuid) {
			if ($feedback = $this->getFeedbackByUuid($uuid)) {
				$session = new Zend_Session_Namespace($sessionNs, true);
				// cookie expires in 14 days
				if ($request->getParam('uuid')) {
					// only set cookie if it is not already set
					setcookie('feedback_code', $uuid, time() + (14 * 3600 * 24), '/', $conference['hostname']);
				}
				return $this->_feedback_id = $session->feedback_id = (int) $feedback->code_id;
			}
		}

		// If no UUID is found in Request, Cookie or Session then return
		return false;
	}

	/**
	 * Get feedback by UUID
	 * @param	integer		$uuid		UUID
	 * @return	object		Zend_Db_Table_Row
	 */
	public function getFeedbackByUuid($uuid)
	{
		$row = $this->getResource('feedbackcodes')->getFeedbackByUuid( $uuid );
    	if ($row === null) {
    		throw new TA_Model_Exception('Feedback code not found');
    	}
    	return $row;
	}

	/**
	 * Load feedback data by id
	 *
	 * @param	integer		$id			feedback id
	 * @param	string		$section	feedback section (db table)
	 * @return	mixed		null or Zend_Db_Table_Rowset
	 */
	 public function getFeedbackById($id, $section)
	 {
	 	$section = 'feedback'.$section;

		$row = $this->getResource($section)->getFeedbackById( $id );
    	return $row;
	 }

	/**
	 * Lazy load feedback_id. This method can be used to check
	 * if a user has 'authenticated'
	 *
	 * @return	integer		feedback_id
	 */
	public function getFeedbackId()
	{
		if (!$this->_feedback_id) {
    		$this->_feedback_id = $this->_init();
		}
		return $this->_feedback_id;
	}

	/**
	 * Utility function
	 * @return array
	 */
	 public function getPostArray()
	 {
	 	return $this->_post;
	 }

	/**
	 * Get Conference Participants
	 * This should hook in to your registration system
	 *
	 * @return	array
	 */
	public function getParticipants()
	{
		if (!$this->checkAcl('participants')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		// for debugging just use me
		$participants = array(
			array(
				'email' => 'gijtenbeek@terena.org',
				'fname' => 'Christian',
				'lname' => 'Gijtenbeek'
			),
			array(
				'email' => 'zeker weten@(fout.nl',
				'fname' => 'Mo',
				'lname' => 'Dahhouch'
			),
			array(
				'email' => 'visser@terena.org',
				'fname' => 'Mo',
				'lname' => 'sdfsd'
			)	
		);
		
		// this is really specific to TERENA, get participants from webshop (remote db)
		#$config = new Zend_Config_Ini(
		#    APPLICATION_PATH.'/configs/web.ini',
		#    'development'
		#);
		#$db = Zend_Db::factory($config->resources->multidb->webshop);
		#
		#$query = "select fname, lname, email from vw_prodpart
		#where product_id IN (92,93,94,95,96,97,98,99) and order_status NOT IN ('canceled', 'unpaid', 'pending', 'refund')";
		#
		#$participants = $db->query($query)->fetchAll();		

		// generate feedback codes
		$codes = $this->getResource('feedbackcodes')->createFeedbackCodes(
			count($participants),
			false
		);

		// add uuid to participants array
		foreach ($participants as $key => $participant) {
			$participants[$key]['uuid'] = array_pop($codes);
		}

		return $participants;
	}

	/**
	 * Generate a single feedback code
	 *
	 * @return	string	UUID (feeback code)
	 */
	public function createFeedbackCode()
	{
		return current($this->getResource('feedbackcodes')->createFeedbackCodes(1, false));
	}

	/**
	 * Save feedback data to multiple resources (feedback sections)
	 *
	 * @param		array	$post		Post request
	 * @param		string	$section	Feedback section
	 * @return		mixed	The primary key of the inserted/updated record or resource error message
	 */
	public function saveFeedback(array $post, $section)
	{
		// this is needed for acl assertion. Other option would be to use Request variable
		// from front controller
		$this->_post = $post;

		// perform ACL check
		if (!$this->checkAcl('save')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

        // get different form based on section parameter
		$formName = 'feedback'.ucfirst($section);
		$form = $this->getForm($formName);

		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		// get filtered values
		$values = $form->getValues();

		$feedback = array_key_exists('id', $values) ?
			$this->getResource('feedback'.$section)
				 ->getFeedbackById($values['id']) : null;

		return $this->getResource('feedback'.$section)->saveRow($values, $feedback);
	}

	/**
	 * Rate individual presentations
	 *
	 * @param	integer		$codeId		Feedback code id (pk from feedback.codes)
	 * @param	array		$values		Should contain presentation_id and rating
	 * @return	mixed		The rating value
	 */
	public function ratePresentation($codeId, array $values)
	{
		$presentationId = (int) $values[1];
		$rating = (int) $values[2];

		// zero is no integer, so I can't add it to the min_range
		// using FLOAT is not advisable either
		if ($rating !== 0) {
			// perform some basic validation
			if (!filter_var($rating, FILTER_VALIDATE_INT, array(
				'options' => array(
					'min_range' => 1,
					'max_range' => 5
				)
			))) {
				return false;
			}
		}

		// build values array
		$values = array(
			'id' => $codeId,
			'presentation_id' => $presentationId,
			'rating' => $rating
		);

		// get row if it exists
		$feedback = $this->getResource('feedbackpresentations')
			->getFeedbackById($codeId, $presentationId);

		// delete row if rating is zero
		if ($rating === 0 && $feedback) {
			$feedback->delete();
		} else {
			$this->getResource('feedbackpresentations')->saveRow($values, $feedback);
		}
		return $rating;
	}

	/*
	* Get all presentation ratings
	*
	* @param	integer		$codeId		Feedback code id (pk from feedback.codes)
	* @return array
	*/
	public function getPresentationRatings($codeId)
	{
		return $this->getResource('feedbackpresentations')->getPresentationRatingsByCodeId($codeId);
	}

	/**
	 * Download feedback results
	 *
	 * @param	string		$section	Name of the feedback section
	 * @return	array
	 */
	public function getResults($section)
	{
		$method = 'getFeedback'.ucfirst($section);

		$results = $this->getResource('feedback'.$section)->$method();
		return $results;
	}

	/**
	 * Vote for a poster 
	 *	 
	 * @param	integer		$codeId		Feedback code id (pk from feedback.codes)
	 * @param	integer		$id
	 * @return	boolean
	 */
	public function votePoster($codeId, $posterId = null)
	{
		if (!$posterId) {
			return false;
		}
		$posterId = (int) $posterId;	
		
		// build values array
		$values = array(
			'id' => $codeId,
			'poster_id' => $posterId,
			'rating' => 5
		);
		
		// get row if it exists
		$feedback = $this->getPosterVote($codeId);		
		if ($feedback) {
			$feedback->delete();
		}
		
		$this->getResource('feedbackposters')->saveRow($values);	
	}
		
	/*
	* Get poster vote
	*
	* @param	integer		$codeId		Feedback code id (pk from feedback.codes)
	* @return array
	*/	
	public function getPosterVote($codeId)
	{
		return $this->getResource('feedbackposters')->getFeedbackByCodeId($codeId);	
	}	

}







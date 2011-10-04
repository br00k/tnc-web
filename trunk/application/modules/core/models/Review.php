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

/** 
 *
 * @package Core_Model
 */
class Core_Model_Review extends TA_Model_Acl_Abstract
{

	/**
	 * Get submission by id
	 * @param		integer		$id		User id
	 * @return		Core_Resource_User_Item
	 */
	public function getReviewById($id)
	{
		$row = $this->getResource('reviews')->getReviewById( (int) $id );
    	if ($row === null) {
    		throw new TA_Model_Exception('id not found');
    	}
    	return $row;
	}

	/**
	 * Get a list of reviews
	 * @param		integer		$page	Page number to show
	 * @param		array		$order	Array with keys 'field' and 'direction'
	 * @param		integer		$filter	filter to apply to grid
	 * @return		array		Grid array with keys 'cols', 'primary', 'rows'
	 */
	public function getReviews($paged, $order, $filter=null)
	{
		if (!$this->checkAcl('list')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }
        
		$conference = Zend_Registry::getInstance()->conference;

		$now = new Zend_Date();
		
		// only show users' own reviews if now is earlier than configured date
		#if ($conference['review_visible']->isEarlier($now)) {
			if ($this->getIdentity()->role != 'admin') {
				#$filter->user_id = $this->getIdentity()->user_id;
			}
		#}

		return $this->getResource('reviewsview')->getReviews($paged, $order, $filter);
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

		return $this->getReviewById($id)->delete();
	}

	/**
	 * Get list of reviewers for mass email
	 *
	 * @param	$todo	boolean		Only show submissions that should still be reviewed	
	 * @return	array
	 */
	public function getReviewersForMail($todo = false)
	{
		if (!$this->checkAcl('mail')) {
			throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		$submissions = $this->getResource('submissions')->getSubmissions();
		return $submissions['rows']->getReviewersSubmissions($todo);
	}

	/**
	 * Save submission to resource
	 *
	 * @param		array	$post	Post request
	 * @param		string	$action	The subform part to validate against
	 * @return		mixed	The primary key of the inserted/updated record or resource error message
	 */
	public function saveReview(array $post, $action = null)
	{
		// perform ACL check
		if (!$this->checkAcl('save')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

        // get different form based on action parameter
		$formName = ($action) ? 'review' . ucfirst($action) : 'review';
		$form = $this->getForm($formName);

		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		// get filtered values
		$values = $form->getValues();
		$values['user_id'] = $this->getIdentity()->user_id;

		$review = array_key_exists('review_id', $values) ?
			$this->getResource('reviews')
				 ->getReviewById($values['review_id']) : null;

		return $this->getResource('reviews')->saveRow($values, $review);


	}


}









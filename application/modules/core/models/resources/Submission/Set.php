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
	 * @revision   $Id: Set.php 41 2011-11-30 11:06:22Z gijtenbeek@terena.org $
	 */

/**
 * Submission rowset
 *
 * @package Core_Resource
 * @subpackage Core_Resource_Submission
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Submission_Set extends Zend_Db_Table_Rowset_Abstract
{
	/**
	 * ?? what is this doing here? Is this called from anywhere??
	 *
	 */
	public function getReviewers()
	{
		$query = "select u.email, rs.reviewer_submission_id as id from reviewers_submissions rs
		left join users u on (rs.user_id = u.user_id)
		where rs.submission_id=:submission_id";

		return $this->getTable()->getAdapter()->query(
			$query, array(':submission_id' => $this->submission_id)
		)->fetchAll();
	}

	/**
	 * @todo: This can be removed?? belongs in Item.php
	 *
	 */
	public function getSubmissionOneliner()
	{
		return $this->title;
	}
	
	/**
	 * Get total number of reviewers for a submission
	 * @return array
	 */
	public function getNumberOfReviewers()
	{
		$query = "select submission_id, count(*) from reviewers_submissions group by reviewers_submissions.submission_id";
		return $this->getTable()->getAdapter()->fetchPairs($query);
	}


}
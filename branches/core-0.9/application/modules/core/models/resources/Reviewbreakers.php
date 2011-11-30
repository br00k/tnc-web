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
 * @revision   $Id: Eventlogs.php 30 2011-10-06 08:37:15Z gijtenbeek@terena.org $
 */

/**
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Reviewbreakers extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'reviewbreaker';

	protected $_primary = 'submission_id';

	public function init() {}

	public function getItemById($id)
	{
		return $this->find($id)->current();
	}

	/**
	 * Get tiebreaker submissions
	 *
	 * @param 	integer	$userId
	 * @param	array	$submissionIds
	 * @return	array
	 */
	public function getAllTiebreakers($userId, $submissionIds = array())
	{
		$query = "select r.submission_id,
		count(r.submission_id) as review_count,
		count(CASE WHEN r.self_assessment=1 THEN 1 ELSE NULL END) as wrong_reviewer_count,
		rb.evalue,
		count(case when r.user_id=$userId then 1 else null end) as my_review
		from reviews r
		left join reviewbreaker rb on (r.submission_id = rb.submission_id)
		group by r.submission_id, rb.evalue
		order by r.submission_id";

		return $this->getAdapter()->fetchAssoc($query);
	}

}
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
 * @revision   $Id: Item.php 41 2011-11-30 11:06:22Z gijtenbeek@terena.org $
 */

/**
 * User row, implements Zend_Acl_Role_Interface so this object is a role
 *
 * @package Core_Resource
 * @subpackage Core_Resource_User
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_User_Item extends TA_Model_Resource_Db_Table_Row_Abstract implements Zend_Acl_Role_Interface
{

	/**
	 * This is called only during authentication.
	 * Add extra attributes (role, submissions etc.) to data array
	 * and set lastlogin date
	 * @return void
	 */
	public function updateAttributes()
	{
		// update lastlogin timestamp
		$this->lastlogin = 'now()';
		$this->save();

		// safety
		$this->setReadOnly(true);

		// roles
		$query = "select r.name from user_role ur left join roles r on (ur.role_id=r.role_id) where user_id=:user_id";
		$roles = $this->select()->getAdapter()->fetchCol($query, array(':user_id' => $this->user_id));
		// logged in users automatically get user role
		if (empty($roles)) {
			$this->_data['role'] = array('user');
		} else {
			$this->_data['role'] = $roles;
		}

		// sessions to chair
		$query = "select su.session_id from sessions_users su where su.user_id=:user_id";
		$this->_data['sessions_to_chair'] = $this->select()->getAdapter()->fetchCol($query, array(':user_id' => $this->user_id));

		// submissions to review
		$query = "select rs.submission_id, tiebreaker from reviewers_submissions rs where rs.user_id=:user_id";
		$this->_data['submissions_to_review'] = $this->select()->getAdapter()->fetchPairs($query, array(':user_id' => $this->user_id));

		// my own submissions
		$query = "select s.submission_id, s.title, s.date, s.file_id from submissions s left join users_submissions us ON (s.submission_id = us.submission_id) where us.user_id=:user_id";
		$this->_data['my_submissions'] = $this->select()->getAdapter()->fetchAssoc($query, array(':user_id' => $this->user_id));

		// my own presentations
		$query = "select p.presentation_id from presentations p left join presentations_users pu on (p.presentation_id = pu.presentation_id) where pu.user_id=:user_id";
		$this->_data['my_presentations'] = $this->select()->getAdapter()->fetchCol($query, array(':user_id' => $this->user_id));

	}

	/**
	 * Reload session
	 *
	 * @return void
	 */
	public function reloadSession()
	{
		$this->updateAttributes();
		// make sure only current session is updated, otherwise you login as other user!
		if (Zend_Auth::getInstance()->getIdentity()->user_id === $this->user_id) {
			$st = Zend_Auth::getInstance()->getStorage();
			Zend_Auth::getInstance()->getStorage()->write($this);
		}
	}

	/**
	 * Returns all user data apart from saml_uid_attribute
	 *
	 * @return array
	 */
	public function getSafeUser()
	{
		$data = $this->toArray();
		unset($data['uid']);
		return $data;
	}

	/**
	 * Get full name
	 *
	 * @return string
	 */
	public function getFullName()
	{
		return $this->fname.' '.$this->lname;
	}

	/**
	 * Get identifier string
	 *
	 * This is for use in lists, so that it is possible to distinguish
	 * between "Joe Average <joe.a@uni.edu>" from f.i. Google, and
	 * "Joe Average <joe.a@uni.edu>" from the UNI IdP.
	 * Some specific smart_id logic, if that doesn't work just use the
	 * bare saml_uid_attribute.
	 *
	 * @return string
	 */
	public function getOneliner()
	{
		if (preg_match('/^([a-z]+)_targetedID:http/', $this->uid, $matches)) {
			$idp = ucfirst($matches[1]);
		} elseif (preg_match('/.*!(.*)$/', $this->uid, $matches)) {
			$idp = $matches[1];
		} else {
			$idp = $this->uid;
		}
		return $this->fname.' '.$this->lname.' ('.$this->email.', '.$idp.')';
	}

	/**
	 * Check if user has a certain role
	 *
	 * @param	$requiredRole	string/array
	 * @return	boolean|null
	 */
	public function hasRole($requiredRole)
	{
		// normalize $requiredRole to array
		if (!is_array($requiredRole)) {
			$requiredRole = array($requiredRole);
		} else if (0 === count($requiredRole)) {
			$requiredRole = array(null);
		}

		$roles = $this->getRoles(true);

		foreach ($requiredRole as $rrole) {
			if (in_array($rrole, $roles)) {
				return true;
			}
		}

	}

	/**
	 * Get role id - this method also deals with multiple roles
	 *
	 * @return string
	 */
	public function getRoleId()
	{
		// role is not set, so user is a guest
		if (!isset($this->role)) {
			return 'guest';
		}

		// user has multiple roles, create dummy role and apply inheritance
		if (count($this->role) > 1) {
			$acl = Zend_Registry::get('acl');
			if (!$acl->hasRole('current')) {
				$acl->addRole(new Zend_Acl_Role('current'), $this->role);
			}
			return 'current';
		}

		// user has only one role
		return $this->role[0];
	}

	/**
	 * Get submissions that the user is assigned reviewer of
	 *
	 * @param	boolean		$full	Return full live dataset or minimal cached set?
	 * @param	boolean		$excludeReviewed		Exclude submissions that user reviewed
	 *
	 * @return array in format (int) submission_id => (boolean) tiebreaker
	 */
	public function getSubmissionsToReview($full = false, $excludeReviewed = false)
	{
		if ($full) {
			$where = ($excludeReviewed)
				? "where rs.user_id=$this->user_id and r.user_id!=$this->user_id"
				: "where rs.user_id=$this->user_id";

			$query = "select rs.submission_id, rs.tiebreaker, rb.evalue, count(
			CASE WHEN r.self_assessment=1 THEN 1 ELSE NULL END
			) as wrong_reviewer_count,
			count (r.submission_id) as review_count,
			count(case when r.user_id=$this->user_id then 1 else null end) as my_review
			from reviewers_submissions rs left join reviewbreaker rb on (rs.submission_id = rb.submission_id)
			left join reviews r on (r.submission_id = rs.submission_id)
			$where
			group by rs.submission_id, rs.tiebreaker, rb.evalue
			order by rs.submission_id";

			return $this->select()->getAdapter()->fetchAssoc($query);
		}
		return $this->submissions_to_review;
	}

	/**
	 * Get sessions that the user chairs
	 *
	 * @return array
	 */
	public function getSessionsToChair()
	{
		return $this->sessions_to_chair;
	}

	/**
	 * Get submissions of the user
	 *
	 * @return array
	 */
	public function getMySubmissions()
	{
		return $this->my_submissions;
	}

	/**
	 * Get presentations of the user
	 *
	 * @return array
	 */
	public function getMyPresentations()
	{
		return $this->my_presentations;
	}

	/**
	 * Is user admin?
	 * @return boolean
	 */
	public function isAdmin()
	{
		return in_array('admin', $this->role, true);
	}

 	/**
 	 * Get role of user
 	 *
 	 * @param		boolean	$nameOnly	Return only the role name
 	 * @return		array				Array of roles
 	 */
	public function getRoles($nameOnly = false)
	{
		$db = $this->select()->getAdapter();
		$query = "select r.name, ur.user_role_id from user_role ur right join roles r on (ur.role_id = r.role_id) where ur.user_id=:user_id";
		if ($nameOnly) {
			return $db->fetchCol($query, array(':user_id' => $this->user_id));
		}
		return $db->fetchAll($query, array(':user_id' => $this->user_id));
	}

	public function getAuditData()
	{
		return $this->select()->getAdapter()->fetchRow("select fname, lname, email from useraudit where user_id=".$this->user_id);
	}

	/**
	 * Adds a role to a user
	 *
	 * @param	string	$role
	 * @return	mixed
	 */
	public function addRoleByName($role)
	{
		$adapter = $this->select()->getAdapter();

		$query = "select role_id from roles where name=:rolename";
		if (!$roleId = $adapter->fetchOne($query, array('rolename' => $role))) {
			throw new Exception("role ($role) not found - make sure this role is in the roles table.");
		}

		$values = array('user_id' => $this->user_id, 'role_id' => $roleId);

		$query = "select * from user_role where user_id=:user_id and role_id=:role_id";
		if ($adapter->fetchOne($query, $values)) {
			return;
		}

		$query = "insert into user_role (user_id, role_id) values (:user_id, :role_id)";
		return $adapter->query($query, $values);
	}

	/**
	 * Get sessions current user is chairing
	 *
	 */
	public function getSessions()
	{
		$query = "select * from vw_sessions_chairs where user_id=:user_id";
		return $this->select()->getAdapter()->fetchAll($query, array(':user_id' => $this->user_id));
	}

	/**
	 * Get presentations current user is a speaker of
	 *
	 */
	public function getPresentations()
	{
		$query = "select * from vw_presentations_speakers where user_id=:user_id and email=:email";
		return $this->select()->getAdapter()->fetchAll($query, array(
			':user_id' => $this->user_id,
			':email' => $this->email
		));
	}

	/**
	 * Is user tiebreaker?
	 *
	 * @param	integer		$id		reviewer_submission_id
	 * @return	boolean
	 */
	public function isReviewTiebreaker($id)
	{
		$query = "select tiebreaker from reviewers_submissions where reviewer_submission_id=:id";
		return $this->select()->getAdapter()->fetchOne($query, array(':id' => $id));
	}

}
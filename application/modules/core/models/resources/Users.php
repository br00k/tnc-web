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
 * @revision   $Id: Users.php 91 2012-12-10 15:39:51Z gijtenbeek@terena.org $
 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Users extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'users';

	protected $_primary = 'user_id';

	protected $_rowClass = 'Core_Resource_User_Item';
	
	protected $_rowsetClass = 'Core_Resource_User_Set';

	private $_config;

	public function init() {
		$this->_config = Zend_Registry::get('config');
		$this->attachObserver(new Core_Model_Observer_User());
	}

	/**
	 * Gets user by primary key
	 *
	 * @param integer $id
	 * @return object Core_Resource_User_Item
	 */
	public function getUserById($id)
	{
		return $this->find((int) $id)->current();
	}

	/**
	 * Get user by email
	 *
	 * @param	string	$email
	 * @return	Core_Resource_User_Item
	 */
	public function getUserByEmail($email)
	{
		$select = $this->select();
		$select->where('email = ?', $email);

		return $this->fetchRow($select);
	}

	/**
	 * Map federated attributes to User database fields
	 * This requires the use of the SmartAttrs authproc filter in SimpleSAMLphp
	 * to do the processing. This function is limited to only checking the
	 * the existence of all the required attributes.
	 *
	 * @param array $federatedAttributes array of federated attributes
	 * @return array
	 */
	public function mapFederatedToUser(array $federatedAttributes)
	{
		$required = array(
//			'saml_uid_attribute',
			'uid',
			'fname',
			'lname',
			'organisation',
			'email',
			'country',
		);

		foreach ($required as $req) {
			$configattr = 'saml_'.$req.'_attribute';
			$required_in_saml[$req] = $this->_config->simplesaml->$configattr;
		}

		$missing = array_diff($required_in_saml, array_keys($federatedAttributes));

		if (count($missing) > 0) {
			throw new TA_Exception("Missing required attribute(s): ".implode(', ', $missing).". This/these HAVE to be provided by the IdP, possibly by using the SmartAttr module.");
		}

		foreach ($required_in_saml as $req=>$req2) {
			$values[$req] = $federatedAttributes[$req2][0];
		}

		// Do some more magic here, maybe try to get rid of the s_mart_id module in SimpleSAML?
		return $values;
	}

	/**
	 * @param	$smartid
	 */
	public function getUserBySmartId($smart_id, $safe = false)
	{
		$select = $this->select();
		$select->where('uid = ?', $smart_id);

		$row = $this->fetchRow($select);

		return $row;
	}
	
	/**
	 * Search for string within user table
	 * @return	array		Array of user_id
	 */
	 public function searchUser($string)
	 {
	 	$query = "select user_id from users where lname ilike '%".$string."%' 
	 	or fname ilike '%".$string."%'";
		$users = $this->getAdapter()->fetchCol(
			$query
		);
		
		if (!empty($users)) { 
			return $users;
		}
	 }

	/**
	 * Get user by invite UUID
	 *
	 * @param	string	$hash	UUID
	 * @return	Core_Resource_User_Item
	 */
	public function getUserByInvite($hash)
	{
		return $this->fetchRow(
			$this->select()
				 ->where('invite = ?', $hash)
				 ->where("inserted > now() - INTERVAL ?", $this->_config->core->userInviteTtl)
		);
	}

	/**
	 * Get role(s) of a user
	 *
	 * @param	integer	$id		user_id
	 * @return	array	Role(s)
	 */
	public function getRolesOfUser($id)
	{
		$query = "select r.name from user_role ur right join roles r on (ur.role_id = r.role_id) where ur.user_id=:user_id";
		return $this->getAdapter()->fetchCol($query, array(':user_id' => $id));
	}

	/**
	 * Gets email/user_id data
	 *
	 * @return	array
	 */
	public function getUsersForSelect()
	{
		return $this->getAdapter()->fetchPairs(
			$this->select()
				 ->from('users', 'user_id')
				 ->columns('email')
				 ->order('lower(email) ASC')
		);
	}

	/**
	 * Save user role in reference table
	 *
	 * @param	integer	$id		user_id
	 * @param	string	$role	user role
	 */
	public function addUserRole($id, $role)
	{
		if ($user = $this->getUserById($id)) {
			return $user->addRoleByName($role);
		}
	}

	public function getUsers($paged = null, $order = array(), $filter = null)
	{
		$grid = array();
		$grid['cols'] = $this->getGridColumns();
		$grid['primary'] = $this->_primary;

		$select = $this->select();

		if (!empty($order[0])) {
			$order = 'lower('.$order[0].') '.$order[1];
		} else {
			$order = 'lower(lname) ASC';
		}
		$select->order($order)
			   ->from($this->_name, array_keys($this->getGridColumns()));

		// apply filters to grid
		if ($filter) {
			foreach ($filter as $field => $value) {
				if (is_array($value)) {
					$select->where($field.' IN (?)', $value);
				} else {
					$select->where($field.' = ?', $value);
				}
			}
		}

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
			// user_id is hidden so I don't have to provide a label
			'user_id' => array('field' => 'user_id', 'sortable' => true, 'hidden' => true),
			'fname' => array('field' => 'fname', 'label' => 'First name', 'sortable' => true),
			'lname' => array('field' => 'lname', 'label' => 'Last name', 'sortable' => true),
			'email' => array('field' => 'email', 'label' => 'Email', 'sortable' => true, 'modifier'=>function($v) {return substr($v, 0, 30); }),
			'organisation' => array('field' => 'organisation', 'label' => 'Organisation', 'sortable' => true),
			'lastlogin' => array('field' => 'lastlogin', 'label' => 'Last login', 'sortable' => true, 'modifier' => 'formatDate'),
			'active' => array('field' => 'active', 'label' => 'Active', 'sortable' => false),
//			'saml_uid_attribute' => array('field' => 'saml_uid_attribute', 'label' => 'saml_uid_attribute', 'sortable' => false, 'hidden' => true),
			'uid' => array('field' => 'uid', 'label' => 'uid', 'sortable' => false, 'hidden' => true),
			'invite' => array('field' => 'invite', 'label' => 'invite', 'sortable' => false, 'hidden' => true),
			'inserted' => array('field' => 'inserted', 'label' => 'inserted', 'sortable' => false, 'hidden' => true)
		);

	}

}

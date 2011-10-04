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
class Core_Resource_Usersview extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'vw_users';

	protected $_primary = 'user_id';

	protected $_rowClass = 'Core_Resource_User_Item';

	public function init() {}

	/**
	 * Gets user by primary key
	 * @return object Core_Resource_User_Item
	 */
	public function getUserById($id)
	{
		return $this->find( (int)$id )->current();
	}

	public function getUsers($paged=null, $order=array(), $filter=null)
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
			   ->from( 'vw_users', array_keys($this->getGridColumns()) );

		if ($filter) {
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
			'email' => array('field' => 'email', 'label' => 'Email', 'sortable' => true),
			//'email' => array('field' => 'email', 'label' => 'Email', 'sortable' => true, 'modifier'=>function($v){return substr($v, 0, 30);}),
			'organisation' => array('field' => 'organisation', 'label' => 'Organisation', 'sortable' => true),
			'lastlogin' => array('field' => 'lastlogin', 'label' => 'Last login', 'sortable' => true, 'modifier' => 'formatDate'),
			'active' => array('field' => 'active', 'label' => 'Active', 'sortable' => false),
//			'saml_uid_attribute' => array('field' => 'saml_uid_attribute', 'label' => 'saml_uid_attribute', 'sortable' => false, 'hidden' => true),
			'uid' => array('field' => 'uid', 'label' => 'uid', 'sortable' => false, 'hidden' => true),
			'invite' => array('field' => 'invite', 'label' => 'invite', 'sortable' => false, 'hidden' => true),
			'inserted' => array('field' => 'inserted', 'label' => 'inserted', 'sortable' => false, 'hidden' => true),
			'role_name' => array('field' => 'role_name', 'label' => 'role_name', 'sortable' => false, 'hidden' => true)
		);

	}
}
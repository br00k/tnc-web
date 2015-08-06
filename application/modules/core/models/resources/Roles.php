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
 * @revision   $Id: Roles.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Roles extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'roles';

	protected $_primary = 'role_id';

	protected $_rowClass = 'Core_Resource_Role_Item';
	
	protected $_rowsetClass = 'Core_Resource_Role_Set';

	public function init() {}

	public function getRoleById($id)
	{
		return $this->find((int) $id)->current();
	}

	/**
	 * Get role id by role name
	 *
	 * @return mixed	Core_Resource_Role_Item or false 
	 */
	public function getRoleIdByName($name)
	{
		$role = $this->fetchRow(
			$this->select()
				 ->where('name = ?', $name)
		);
		if ($role) {
			return $role->role_id;
		}
		return false;
	}
	
	/**
	 * Get roles for use in form select
	 *
	 * @param	string	$empty	Empty string to prepend to array
	 * @return	array
	 */
	public function getRolesForSelect($empty = null)
	{
		$return = array();

		$return = $this->getAdapter()->fetchPairs($this->select()
			->from('roles', array('role_id', 'name'))
			->order('role_id DESC')
		);

		if ($empty) {
			$return[0] = $empty;
			asort($return);
		}

		return $return;
	}


}
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
 * @author Christian Gijtenbeek
 */
class Core_Model_User extends TA_Model_Acl_Abstract
{

	public function getAuthSources()
	{
		return array(
			'test' => 'test',
			'default-sp' => 'TERENA'
		);
	}


	/**
	 * Get user by id
	 * @param		integer		$id		User id
	 * @return		Core_Resource_User_Item
	 */
	public function getUserById($id)
	{
		$row = $this->getResource('users')->getUserById( (int) $id );
    	if ($row === null) {
    		throw new TA_Model_Exception('id not found');
    	}
    	return $row;
	}

	/**
	 * Get user by email address
	 * @param		string		$email	Email address
	 * @return		Core_Resource_User_Item
	 */
	public function getUserByEmail($email)
	{
		return $this->getResource('users')->getUserByEmail( $email );
	}

	/**
	 * Get user by Smart ID
	 * @param		string		$smartid SmartID from SimpleSAML authproc
	 * @return		Core_Resource_User_Item
	 */
	public function getUserBySmartId($smartid)
	{
		return $this->getResource('users')->getUserBySmartId( $smartid );
	}

	/**
	 * Gets array of users for use in select box.
	 *
	 * @param 	boolean	$addEmpty Add empty value to array?
	 * @param 	string	$role role
	 * @return	array
	 */
	public function getUsersForSelect($addEmpty = null, $role = null)
	{
		$users = $this->getUsersWithRole(null, null, $role);

		$userArray = array();
		if ($addEmpty) {
			$userArray[0] = '---';
		}
		foreach ($users['rows'] as $user) {
			$userArray[$user->user_id] = $user->getOneliner();
		}

		return $userArray;
	}


	/**
	 * Get a list of users
	 * @param		integer		$page	Page number to show
	 * @param		array		$order	Array with keys 'field' and 'direction'
	 * @return		array		Grid array with keys 'cols', 'primary', 'rows'
	 */
	public function getUsers($paged, $order, $filter=null)
	{
		if (!$this->checkAcl('list')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		return $this->getResource('users')->getUsers($paged, $order, $filter);
	}

	/**
	 * Get a list of users with certain role
	 * @param		string		$role	Name of role that user should have
	 */
	public function getUsersWithRole($paged, $order, $role)
	{
		if (!$role) {
			throw new TA_Model_Exception('Please provide a role');
		}
		if ( $this->getResource('roles')->getRoleIdByName($role) ) {
			$filter = new stdClass();
			$filter->filters = new stdClass();
			$filter->filters->role_name = $role;
			return $this->getResource('usersview')->getUsers($paged, $order, $filter);
		}
		return false;
	}

	/**
	 * Get role of user
	 * @param		integer		$id		User id
	 * @return		array		Array of roles
	 */
	public function getRolesOfUser($id)
	{
		if (!$this->checkAcl('showRoles')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

        return $this->getResource('users')->getRolesOfUser($id);
	}

	public function getRolesForSelect($addEmpty = null)
	{
		if (!$this->checkAcl('showRoles')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

        return $this->getResource('roles')->getRolesForSelect($addEmpty);
	}

	/**
	 * Remove user from resource
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

		return $this->getUserById($id)->delete();
	}

	/**
	 * Save user to resource
	 *
	 * @param		array	$post	Post request
	 * @param		string	$action	The subform part to validate against
	 * @return		mixed	The primary key of the inserted/updated record or resource error message
	 */
	public function saveUser(array $post, $action = null)
	{
		if (!$this->checkAcl('save')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

        // get different form (needed to validate values against) based on action parameter
		$formName = ($action) ? 'user' . ucfirst($action) : 'userInvite';
		$form = $this->getForm($formName);

		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		if ( $form->file->isUploaded() ) {

			// save file to filesystem
			try {
				$fileInfo = array();
				$adapter = $form->file->getTransferAdapter();
			    $hash = $adapter->getHash('sha1');

			    $form->file->addFilter('rename', array(
			        'target' => Zend_Registry::get('config')->directories->uploads.$hash,
			        'overwrite' => true // @todo: set to FALSE when debugging is over!
			    ));

			    $origName = $adapter->getFileName();
			    $adapter->receive();
				$fileInfo = $adapter->getFileInfo();
				$fileInfo['file']['_filename_original'] = $origName;
				$fileInfo['file']['_filehash'] = $hash;
				$fileInfo['file']['_filetype'] = 2;
			} catch (Zend_File_Transfer_Exception $e) {
				$e->getMessage();
			}

		}

		$db = $this->getResource('files')->getAdapter();
		$db->beginTransaction();

		try {
			// get filtered values
			$values = $form->getValues();

			if ( $form->file->isUploaded() ) {
				// persist file
				$fileId = $this->getResource('files')->saveRow($fileInfo);
				$values['file_id'] = $fileId;
			}

			// if user_id is set, then get user object and pass it as parameter to the saveRow method
			$user = array_key_exists('user_id', $values) ?
				$this->getResource('users')
					 ->getUserById($values['user_id']) : null;

			$userId = $this->getResource('users')->saveRow($values, $user);

			// Give user a role
			if ($this->checkAcl('roleSave')) {
				if ( array_key_exists('role_id', $values) ) {
					$data = array('user_id' => $userId, 'role_id' => $values['role_id']);
					if (!$this->getResource('userroles')->getItemByValues($data)) {
					    $this->getResource('userroles')->saveRow($data);
					}
				}
        	}

        	// store invitee data in audit table
        	if (!$user) {
        		$values['user_id'] = $userId;
        		$this->saveUseraudit($values);
        	}

			$db->commit();
			if ($user) {
				$user->reloadSession();
			}
			return $userId;

		} catch (Exception $e) {
			$db->rollBack();
			throw new TA_Model_Exception($e->getMessage());
		}
	}

	/**
	 * Save user audit data
	 *
	 */
	public function saveUseraudit($values)
	{
		if (!$this->checkAcl('audit')) {
			throw new TA_Model_Acl_Exception("Insufficient rights");
        }
        $this->getResource('useraudit')->saveRow($values);
	}


	/**
     * Save user/role link
     *
     * @param	array	$post	Post variable
     * @return	mixed	The primary key of the inserted record if insert went OK
     *					false if user already belongs to this session
     */
	public function saveRoles(array $post)
	{
		if (!$this->checkAcl('roleSave')) {
			throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		$form = $this->getForm('userRole');
		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		$values = $form->getValues();

		$resource = $this->getResource('userroles');

		// if user role link does not already exist, save link
		if (!$resource->getItemByValues($values)) {
			return $resource->saveRow($values);
		} else {
			return false;
		}
	}

	/**
	 * Delete a role from this user
	 *
	 * @param	integer		$id		session_user_id
	 * @return	The number of rows deleted
	 */
	public function deleteRole($id = null)
	{
		if (!$this->checkAcl('roleDelete')) {
            throw new TA_Model_Acl_Exception("Insufficient rights");
        }

		if (!$id) {
			return false;
		}
		$id = (int) $id;

		return $this->getResource('userroles')->getItemById($id)->delete();
	}

	/**
	 * Save a user with federated attributes
	 *
	 * @param	array	$federatedAttributes	Federated attribute array
	 * @param	string	$uuid					Invite uuid
	 *
	 * @return mixed The primary key of the inserted/updated record
	 */
	public function saveUserFromFederatedIdentity(array $federatedAttributes, $uuid = null)
	{
		$users = $this->getResource('users');

		$user = ($uuid) ? $users->getUserByInvite($uuid) : null;

		$values = $users->mapFederatedToUser($federatedAttributes);
		// see https://tracker.terena.org/browse/CORE-245
		if ($uuid) {
			$values['invite'] = null;
		}

		return $users->getUserById(
			$users->saveRow(
				$values,
				$user
			)
		);
	}

}
<?php
class Core_Resource_Users extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'users';

	protected $_primary = 'user_id';

	protected $_rowClass = 'Core_Resource_User_Item';

	public function init() {
		$this->attachObserver(new Core_Model_Observer_User());
	}

	/**
	 * Gets user by primary key
	 * @return object Core_Resource_User_Item
	 */
	public function getUserById($id)
	{
		return $this->find( (int)$id )->current();
	}

	public function getUserByEmail($email)
	{
		$select = $this->select();
		$select->where('email = ?', $email);

		$row = $this->fetchRow($select);

		return $row;
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
			'smart_id',
			'fname',
			'lname',
			'organisation',
			'email',
			'country',
		);

		$missing = array_diff($required, array_keys($federatedAttributes));

		if(count($missing) > 0) {
			throw new Exception("Missing required attribute(s): ".implode(', ', $missing).". This/these HAVE to be provided by the IdP, possibly by using the SmartAttr module.");
		}

		foreach ($required as $req) {
			$values[$req] = $federatedAttributes[$req][0];
		}
		return $values;
	}

	/**
	 * @param	$smartid
	 */
	public function getUserBySmartId($smartid, $safe = false)
	{
		$select = $this->select();
		$select->where('smart_id = ?', $smartid);

		$row = $this->fetchRow($select);

		return $row;
	}

	public function getUserByInvite($hash)
	{
		return $this->fetchRow(
			$this->select()
				 ->where('invite = ?', $hash)
				 ->where('inserted > now() - INTERVAL \'6 months\'')
		);
	}

	public function getUserIdByEmail($email)
	{
		$row = $this->fetchRow(
			$this->select()
				 ->from($this->_name, array('user_id'))
				 ->where('email = ?', $email)
		);
		if ($row) return $row->user_id;

		return false;
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
	 * Get certificates a user is allowed to edit
	 *
	 * @return	mixed	Certificates a user is allowed to edit, or false if none
	 * @todo fingerprint_* must be removed from db table 'certificates'!
	 * @todo not needed?!
	 */
	public function getCertificatesOfUser($id)
	{
		$query = "select * from certificates c inner join users_certificates uc on (c.cert_id = uc.cert_id) where uc.user_id=:user_id";
		return $this->getAdapter()->query($query, array(':user_id' => $id))->fetchAll();
	}

	/**
	 * Save user role in reference table
	 * @param	integer	$id		user_id
	 * @param	string	$role	user role
	 */
	public function addUserRole($id, $role)
	{
		if ( $user = $this->getUserById($id) ) {
			return $user->addRoleByName($role);
		}
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
			   ->from( $this->_name, array_keys($this->getGridColumns()) );

		// apply filters to grid
		if ($filter) {
			foreach ($filter as $field => $value) {
			    if (is_array($value)) {
			        $select->where( $field.' IN (?)', $value);
			    } else {
			        $select->where( $field.' = ?', $value);
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
			'email' => array('field' => 'email', 'label' => 'Email', 'sortable' => true, 'modifier'=>function($v){return substr($v, 0, 30);}),
			'organisation' => array('field' => 'organisation', 'label' => 'Organisation', 'sortable' => true),
			'lastlogin' => array('field' => 'lastlogin', 'label' => 'Last login', 'sortable' => true, 'modifier' => 'formatDate'),
			'active' => array('field' => 'active', 'label' => 'Active', 'sortable' => false),
			'smart_id' => array('field' => 'smart_id', 'label' => 'smart_id', 'sortable' => false, 'hidden' => true),
			'invite' => array('field' => 'invite', 'label' => 'invite', 'sortable' => false, 'hidden' => true),
			'inserted' => array('field' => 'inserted', 'label' => 'inserted', 'sortable' => false, 'hidden' => true)
		);

	}

}

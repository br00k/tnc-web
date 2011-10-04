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
 * @package Core_Forms
 * @subpackage Core_Forms_User
 */
class Core_Form_User_Role extends TA_Form_Abstract
{

	public function init()
	{
		parent::init();

		$this->setAction('/core/user/roles');

	    $id = new Zend_Form_Element_Hidden('user_id');
	    $id->setRequired(true)
		   ->addValidators(
		      array('Int')
		   )
		   ->setDecorators(array('Composite'));

		$userModel = new Core_Model_User();

	    $roles = new Zend_Form_Element_Select('role_id');
	    $roles->setAttrib('class', 'large')
	    	  ->setAttrib('onchange', 'this.form.submit()')
			  ->setMultiOptions($userModel->getRolesForSelect())
			  ->setDecorators(array('Composite'));

	    $this->addElements(array(
	    	$id,
	    	$roles
	    ));

	}

}
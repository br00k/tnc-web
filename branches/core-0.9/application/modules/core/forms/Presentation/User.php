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
 * @revision   $Id: User.php 619 2011-09-29 11:20:22Z gijtenbeek $
 */
 
/** 
 *
 * @package Core_Forms
 * @subpackage Core_Forms_Presentation
 */
class Core_Form_Presentation_User extends TA_Form_Abstract
{

	public function init()
	{
		parent::init();

		$this->setAction('/core/presentation/speakers');

	    $id = new Zend_Form_Element_Hidden('presentation_id');
	    $id->setRequired(true)
		   ->addValidators(
		      array('Int')
		   )
		   ->setDecorators(array('Composite'));

		$presentationModel = new Core_Model_Presentation();

		$users = new TA_Form_Element_User('user_id');		
		$users->setTaController('presentation')
			  ->populateElement('presenter')
			  ->setAttrib('onchange', "this.form.submit()");

	    $this->addElements(array(
	    	$id,
	    	$users
	    ));

	}

}
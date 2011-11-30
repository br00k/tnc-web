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
 * @revision   $Id: Files.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */

/** 
 *
 * @package Core_Forms
 * @subpackage Core_Forms_Session
 */
class Core_Form_Session_Files extends TA_Form_Abstract
{

	public function init()
	{
		parent::init();

		$this->setAction('/core/session/files');
	    $this->setAttrib('enctype', 'multipart/form-data');

	    $id = new Zend_Form_Element_Hidden('session_id');
	    $id->setRequired(true)
		   ->addValidators(
		      array('Int')
		   )
		   ->setDecorators(array('Composite'));

		// the name of this form element must be of an existing filetype
	    $file1 = new TA_Form_Element_MagicFile('slides');
	    $file1->setLabel('Session slide')
			  ->setDescription('')
			  ->addDecorators($this->_magicFileElementDecorator)
			  ->setValueDisabled(true)
			  ->addValidators(array(
			      array('Count', true, 1),
			      array('Size', true, array('max' => '4Mb'))
			  ));

		$subForm = new Zend_Form_SubForm();
		$subForm->addElements(array(
	    	$file1		
		))->setDecorators(array('FormElements'));
		
		$this->addSubForm($subForm, 'files');
	    $this->addElements(array(
	    	$id	    	
	    ));

	    $this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
	    ));

	}

}
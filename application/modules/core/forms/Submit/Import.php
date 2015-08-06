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
 * @revision   $Id: Import.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */

/** 
 *
 * @package Core_Forms
 * @subpackage Core_Forms_Submit
 */
class Core_Form_Submit_Import extends TA_Form_Abstract
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/presentation/import');

		$status = new Zend_Form_Element_Select('status');
		$status->setLabel('With status')
			   ->setAttrib('class', 'small')
			   ->setAttrib('disabled', 'disabled')
			   ->addMultiOptions($this->_getFieldValues('status', 'submit'))
			   ->setDecorators(array('Composite'));

		$submitStart = new Zend_Form_Element_Text('submit_start');
		$submitStart->setLabel('From')
					->setDescription('dd/mm/yy')
					->setAttrib('class', 'small')
					->setRequired(true)
					->setDecorators(array('Composite'));

		$submitEnd = new Zend_Form_Element_Text('submit_end');
		$submitEnd->setLabel('To')
				  ->setDescription('dd/mm/yy')
				  ->setAttrib('class', 'small')
				  ->setRequired(true)
				  ->setDecorators(array('Composite'));

		$setRole = new Zend_Form_Element_Checkbox('set_role');
		$setRole->setLabel('Give user \'presenter\' role')
				->setRequired(false)
				->setChecked(true)
				->setDecorators(array('Composite'));

		$setLink = new Zend_Form_Element_Checkbox('set_link');
		$setLink->setLabel('Make user a speaker for this presentation')
				->setRequired(false)
				->setChecked(true)
				->setDecorators(array('Composite'));

		$sessions = new Zend_Form_Element_Checkbox('link_sessions');
		$sessions->setLabel('Link sessions to presentations')
				 ->setRequired(false)
				 ->setChecked(true)
				 ->setDecorators(array('Composite'));

		$overwrite = new Zend_Form_Element_Checkbox('_overwrite');
		$overwrite->setLabel('Overwrite exsiting presentations')
				  ->setRequired(false)
				  ->setChecked(false)
				  ->setDecorators(array('Composite'));
				  
		$fileLink = new Zend_Form_Element_Checkbox('link_files');
		$fileLink->setLabel('Link files to presentations')
				 ->setRequired(false)
				 ->setChecked(true)
				 ->setDecorators(array('Composite'));
				 
		$this->addElements(array(
			$status,
			$submitStart,
			$submitEnd,
			$setRole,
			$setLink,
			$sessions,
			$fileLink,
			$overwrite
		));

		$this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
		));

	}


}
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
 * @revision   $Id: Topic.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */

/**
 *
 * @package Core_Forms
 */
class Core_Form_Topic extends TA_Form_Abstract
{

	public function init()
	{
		$this->setAction('/core/topic/new');

		$title = new Zend_Form_Element_Text('title');
		$title->setLabel('Title')
			  ->setRequired(true)
			  ->setAttrib('class', 'medium')
			  ->setDescription('Must be between 2 and 100 characters, only letters, numbers and spaces allowed')
			  ->setDecorators(array('Composite'));

		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel('Description')
			  	 ->setRequired(true)
				 ->setAttrib('class', 'medium')
				 ->setDecorators(array('Composite'));


		$this->addElements(array(
			$title,
			$description
		));

		// @todo: by no means secure, you can still manually change the POST array
		// to bypass this
		if (Zend_Auth::getInstance()->getIdentity()->isAdmin()) {
			$this->addElements(array($submission, $logo));
		}

		$this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
		));
	}

}
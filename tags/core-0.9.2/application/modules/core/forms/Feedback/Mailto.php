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
 * @subpackage Core_Forms_Feedback
 */
class Core_Form_Feedback_Mailto extends TA_Form_Abstract
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/feedback/mailto');
		$this->setAttrib('id', 'mailtoform');

	    $email = new Zend_Form_Element_Text('email');
	    $email->setLabel('Email address')
	    	  ->setRequired(true)
	    	  ->setAttrib('class', 'medium')
	    	  ->addValidators(array(
				array('EmailAddress', true)
	    	  ))
	    	  ->setDecorators(array('Composite'));

		$this->addElements(array(
			$email
		));

	    $this->addElement('submit', 'submit', array(
			'label' => 'Send email',
			'decorators' => $this->_buttonElementDecorator
	    ));

	}


}
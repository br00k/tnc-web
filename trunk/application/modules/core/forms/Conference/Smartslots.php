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
 * @subpackage Core_Forms_Conference
 */
class Core_Form_Conference_Smartslots extends TA_Form_Abstract
{

	public function init()
	{
	    $this->setAction('/core/conference/createslots');

	    $this->addElement('hidden', 'id', array(
			'validators' => array(
				array('Int')
			),
			'required' => true,
			'decorators' => $this->_hiddenElementDecorator
	    ));

	    $days = new Zend_Form_Element_Text('days');
	    $days->setLabel('Days')
			 ->setAttrib('class', 'tiny')
			 ->addValidator('Int')
			 ->addFilter('Null')
			 ->setRequired(false)
			 ->setDescription('number of days the conference lasts')
			 ->setDecorators(array('Composite'));

	    $start = new Zend_Form_Element_Text('start');
	    $start->setLabel('Start date of the conference')
			  ->setDescription('dd/mm/yy hh:mm')
			  ->setAttrib('class', 'medium')
			  ->setRequired(false)
			  ->setDecorators(array('Composite'));
			  
		$this->addElements(array(
			$days,
			$start
		));

	    $this->addElement('submit', 'submit', array(
			'label' => 'Create smart defaults',
			'decorators' => $this->_buttonElementDecorator
	    ));
	}

}
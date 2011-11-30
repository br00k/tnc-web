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
 * @subpackage Core_Forms_Session
 */
class Core_Form_Session_Evaluation extends TA_Form_Abstract
{

	public function init()
	{
		parent::init();

		$this->setAction('/core/session/evaluate/format/html');
		$this->setAttrib('id', 'evaluateform');

	    $evaluationId = new Zend_Form_Element_Hidden('session_evaluation_id');
	    $evaluationId->addValidator('Int')
					 ->addFilter('Null')
					 ->setDecorators(array('Composite'));
					 
	    $sessionId = new Zend_Form_Element_Hidden('session_id');
	    $sessionId->setRequired(true)
	    		  ->addValidator('Int')
				  ->setDecorators(array('Composite'));					 

	    $comments = new Zend_Form_Element_Textarea('comments');
	    $comments->setLabel('Comments')
	    		 ->setAttrib('class', 'small')
	    		 ->setRequired(true)
				 ->setDecorators(array('Composite'));
				 
	    $attendees = new Zend_Form_Element_Text('attendees');
	    $attendees->setLabel('Attendees')
				  ->setAttrib('class', 'tiny')
				  ->addValidator('Int')
				  ->addFilter('Null') // add this if you want to provide a blank value
				  ->setRequired(false)
				  ->setDecorators(array('Composite'));	
			 	 
	    $this->addElements(array(
	    	$evaluationId,
	    	$sessionId,
	    	$comments,
	    	$attendees
	    ));

	    $this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
	    ));
	}

}
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
 * @todo abstract getLocationsForSelect() to something: getLocations()->ToSelect();
 */ 
class Core_Form_Session extends TA_Form_Abstract
{

	public function init()
	{
	    $this->setAction('/core/session/new');

	    $title = new Zend_Form_Element_Text('title');
	    $title->setLabel('Title')
			  ->setRequired(true)
			  ->addValidator('regex', true, array(
	    	  	 'pattern' => '/^.*$/',
	    	  	 'messages' => array(Zend_Validate_Regex::NOT_MATCH => 'Wrong format')
	    	  ))
	    	  ->addValidator(new Zend_Validate_Db_NoRecordExists(array(
	    	  	'table' => 'sessions',
	    	  	'field' => 'title'
	    	  )))
	    	  ->setAttrib('class', 'medium')
	    	  ->setDescription('Must be between 2 and 30 characters, only letters, numbers and spaces allowed')
	    	  ->setDecorators(array('Composite'));

	    $desc = new Zend_Form_Element_Textarea('description');
	    $desc->setLabel('Description')
	    	 ->setAttrib('class', 'medium')
	    	 ->setDescription('Please don\'t make your description too long')
	    	 ->setRequired(false)
	    	 ->addValidator('StringLength', true, array(1, 5000,
	    	 	'messages' => array(
					Zend_Validate_StringLength::TOO_SHORT => 'Please provide a longer description',
					Zend_Validate_StringLength::TOO_LONG => 'Your description is too long'
				)
	    	 ))
			 ->setDecorators(array('Composite'));

		$locationModel = new Core_Model_Location();

	    $location = new Zend_Form_Element_Select('location_id');
	    $location->setLabel('Location')
	    		 ->setMultiOptions($locationModel->getLocationsForSelect(1))
				 ->setAttrib('class', 'medium')
	    	 	 ->addFilter('Null')
				 ->setDecorators(array('Composite'));

		$timeslotModel = new Core_Model_Timeslot();

	    $timeslot = new Zend_Form_Element_Select('timeslot_id');
	    $timeslot->setLabel('Timeslot')
	    		 // Only get presentation timeslots
	    		 ->setMultiOptions($timeslotModel->getTimeslotsForSelect(1))
	    		 ->setDescription('dd/mm/yyyy start time - end time')
	    	  	 ->addFilter('Null')
				 ->setAttrib('class', 'medium')
				 ->setDecorators(array('Composite'));

		$this->addElements(array(
			$title,
			$desc
		));

		// quick hack to remove certain elements from form
		// it is however still possible to craft a post request
		// that bypasses this check...
		if (Zend_Auth::getInstance()->getIdentity()->isAdmin()) {
			$this->addElements(array(
				$location,
				$timeslot
			));
		}

	    $this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
	    ));
	}

}
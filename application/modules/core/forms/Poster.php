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
 */
class Core_Form_Poster extends TA_Form_Abstract
{

	public function init()
	{
	    $this->setAction('/core/poster/new');
	    $this->setAttrib('enctype', 'multipart/form-data');

	    $title = new Zend_Form_Element_Text('title');
	    $title->setLabel('Title')
			  ->setRequired(true)
			  ->addValidator('regex', true, array(
	    	  	 'pattern' => '/^.*$/',
	    	  	 'messages' => array(Zend_Validate_Regex::NOT_MATCH => 'Wrong format')
	    	  ))
	    	  ->addValidator(new Zend_Validate_Db_NoRecordExists(array(
	    	  	'table' => 'posters',
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

	    $person = new Zend_Form_Element_Text('persons');
	    $person->setLabel('Persons')
	    	   ->setAttrib('class', 'medium')
	    	   ->setDescription('Please add person(s)')
	    	   ->setDecorators(array('Composite'));

	    $cats = new Zend_Form_Element_Select('category');
	    $cats->setLabel('Category')
	   		 ->setAttrib('class', 'small')
			 ->setMultiOptions($this->_getFieldValues('categories', 'poster'))
			 ->setDecorators(array('Composite'));

	    $file = new TA_Form_Element_MagicFile('file');
	    $file->setLabel('File')
			 ->addDecorators($this->_magicFileElementDecorator)
			 ->addValidators(array(
			     array('Count', true, 1),
			     array('Size', true, array('max' => '5Mb')),
			 ));

		$this->addElements(array(
			$title,
			$desc,
			$person,
			$cats,
			$file
		));

	    $this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
	    ));
	}

}
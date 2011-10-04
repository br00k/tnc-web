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
class Core_Form_Submit extends TA_Form_Abstract
{

	public function init()
	{
	    $this->setAction('/core/submit/new');
		
	    $title = new Zend_Form_Element_Text('title');
	    $title->setLabel('Title of paper')
	    	  ->setRequired(true)
	    	  ->addValidator('StringLength', true, array(2, 150,
				'messages' => array(
					Zend_Validate_StringLength::TOO_SHORT => 'Please provide a longer title',
					Zend_Validate_StringLength::TOO_LONG => 'Your title is too long'
				)
	    	  ))
	    	  ->setAttrib('class', 'medium')
	    	  ->setDescription('Must be between 2 and 150 characters')
			  ->setDecorators(array('Composite'));

	    $audience = new Zend_Form_Element_Radio('target_audience');
	    $audience->setLabel('Please mark the target audience for your presentation')
				 ->setRequired(true)
				 ->setAttrib('class', 'tiny')
		         ->addMultiOptions($this->_getFieldValues('target_audience'))
		         ->setSeparator('<br />')
 				 ->setDecorators(array('Composite'));

	    $publish = new Zend_Form_Element_Radio('publish_paper');
	    $publish->setLabel('Please indicate whether you wish to prepare a full paper for possible publication')
				->setRequired(true)
				->setAttrib('class', 'tiny')
				->addMultiOptions($this->_getFieldValues('publish_paper', 'submit'))
				->setSeparator('<br />')
				->setDecorators(array('Composite'));

	    $comment = new Zend_Form_Element_Textarea('comment');
	    $comment->setLabel('Comment')
	    	 	->setAttrib('class', 'small')
	    	 	->setDescription('Must be between 5 and 1000 characters')
	    	 	->setRequired(false)
	    	 	->addValidator('StringLength', true, array(5, 1000,
	    	 	'messages' => array(
					Zend_Validate_StringLength::TOO_SHORT => 'Please provide a longer description',
					Zend_Validate_StringLength::TOO_LONG => 'Your description is too long'
				)
	    	 	))
			 	->setDecorators(array('Composite'));

	    $file = new TA_Form_Element_MagicFile('file');
	    $file->setLabel('Your submission')
			 ->setRequired(true)
			 ->addDecorators($this->_magicFileElementDecorator)
			 ->setDescription('File must be a maximum of 10Mb')
			 ->addValidators(array(
			    array('Count', true, 1),
			    array('Size', true, 10000000)
			    #array('Extension', true, array('pdf', 'case' => true)),
			    #array('MimeType', false, array('text/plain; charset=us-ascii'))
			 ));

		$subform = new Zend_Form_SubForm();
		$subform->setDecorators(array('FormElements'));

		$subform->addElements(array(
			$title,
			$audience,
			$publish,
			$comment,
			$file
		));
		$this->addSubForm($subform, 'submission');

	    $this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
	    ));
	}

}
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
 * @revision   $Id: Submit.php 76 2012-11-28 17:41:26Z gijtenbeek@terena.org $
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
			  
	    $type = new Zend_Form_Element_MultiCheckbox('submission_type');
	    $type->setLabel('')
				 ->setRequired(false)
				 ->setAttrib('class', 'tiny')
		         ->addMultiOptions($this->_getFieldValues('submission_type'))
		         ->setSeparator('<br />')
 				 ->setDecorators(array('Composite'));
		
	    $title = new Zend_Form_Element_Text('title');
	    $title->setLabel('Title')
	    	  ->setRequired(true)
	    	  ->addValidator('StringLength', true, array(2, 64,
				'messages' => array(
					Zend_Validate_StringLength::TOO_SHORT => 'Please provide a longer title',
					Zend_Validate_StringLength::TOO_LONG => 'Your title is too long'
				)
	    	  ))
	    	  ->setAttrib('class', 'medium')
	    	  ->setDescription('Must be between 2 and 64 characters')
	    	  ->setAttrib('maxLength', 64)
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
				

		$topicModel = new Core_Model_Topic();			
		$topicsForSelect = $topicModel->getTopicsForSelect();
						
	    $topicsel = new Zend_Form_Element_MultiCheckbox('topic');
	    $topicsel->setLabel('Topic')
				 ->setRequired(false)
				 ->setAttrib('class', 'tiny')
	    		 ->setMultiOptions($topicsForSelect)
		         ->setSeparator('<br />')
 				 ->setDecorators(array('Composite'));				
	    
	    $keywords = new Zend_Form_Element_Text('keywords');
	    $keywords->setLabel('Keywords')
	    	  	 ->setRequired(false)
	    	  	 ->addValidator('StringLength', true, array(2, 500,
			  	 	'messages' => array(
			  	 		Zend_Validate_StringLength::TOO_SHORT => 'Please provide longer keywords',
			  	 		Zend_Validate_StringLength::TOO_LONG => 'Your keywords are too long'
			  	 	)
	    	  	 ))
	    	  	 ->setAttrib('class', 'medium')
	    	  	 ->setDescription('Must be between 2 and 500 characters')
			  	 ->setDecorators(array('Composite'));
			  	 
	    $abstract = new Zend_Form_Element_Textarea('abstract');
	    $abstract->setLabel('Submission Summary (If your submission is accepted, this will be publicly visible!)')
	    	 	->setAttrib('class', 'small')
	    	 	->setDescription('Must be between 5 and 2000 characters')
	    	 	->setRequired(false)
	    	 	->addValidator('StringLength', true, array(5, 2000,
	    	 	'messages' => array(
					Zend_Validate_StringLength::TOO_SHORT => 'Please provide a longer abstract',
					Zend_Validate_StringLength::TOO_LONG => 'Your abstract is too long'
				)
	    	 	))
			 	->setDecorators(array('Composite'));


	    $comment = new Zend_Form_Element_Textarea('comment');
	    $comment->setLabel('Information for Reviewers')
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
	    $file->setLabel('Your submission (File must be pdf and no bigger than 10Mb) *')
			 ->setRequired(false)
			 ->addDecorators($this->_magicFileElementDecorator)
			 ->addValidators(array(
			    array('Count', true, 1),
			    array('Size', true, 10000000),
			    array('Extension', true, array('pdf', 'case' => true)),
			    array('MimeType', false, array('application/pdf'))
			 ));
			 
		$file->getValidator('Extension')->setMessage('Only pdf files are allowed!');	 

		$subform = new Zend_Form_SubForm();
		$subform->setDecorators(array('FormElements'));

		$subform->addElements(array(
			$type,
			$file,
			$title,
			$audience,
			#$publish,
			$topicsel,
			$keywords,
			$abstract,
			$comment
		));
		$this->addSubForm($subform, 'submission');

	    $this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
	    ));
	}
	
	/**
	 * Override method to unserialize multiCheckbox values
	 *
	 */
	public function setDefaults(array $defaults)
	{
		$defaults['topic'] = (isset($defaults['topic']) ) ? unserialize($defaults['topic']) : null;
		$defaults['submission_type'] = (isset($defaults['submission_type']) ) ? unserialize($defaults['submission_type']) : null;
		parent::setDefaults($defaults);
	}	

}
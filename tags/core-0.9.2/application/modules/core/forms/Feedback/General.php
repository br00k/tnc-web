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
class Core_Form_Feedback_General extends TA_Form_Abstract
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/feedback/general');

	    $id = new Zend_Form_Element_Hidden('id');
	    $id->setRequired(true)
	       ->setLabel('id')
		   ->addValidators(
			   array('Int')
		   )
		   ->setDecorators(array('Composite'));

	    $confRating = new Zend_Form_Element_Radio('rating');
	    $confRating->setLabel('How would you rate the conference overall?')
				   ->setAttrib('class', 'tiny')
				   ->setMultiOptions($this->_getFieldValues('rating', 'feedback'))
				   ->setDecorators(array('Composite'));

	    $partReasons = new Zend_Form_Element_MultiCheckbox('part_reasons');
	    $partReasons->setLabel('Please select your top three reasons for participating in the TERENA conference')
					->setAttrib('class', 'tiny')
					->setMultiOptions(array(
						'networking' => 'Networking opportunities (i.e. opportunities to form new professional contacts)',
						'collaboration' => 'Collaboration opportunities (i.e. opportunities to work together with others on a common area of interest)',
						'interesting' => 'Interesting topic/speaker that could help me in my job',
						'exposure' => 'Visibility/exposure within the Research & Education Community',
						'management' => 'It is encouraged by my management',
						'support' => 'To support the ongoing development of the research & education community as a whole',
						'other' => 'Other'
					))
					->setDecorators(array('Composite'));

	    $partOther = new Zend_Form_Element_Text('why_other_spec');
	    $partOther->setDescription('Other, please specify')
				  ->setAttrib('class', 'medium')
				  ->setDecorators(array('Composite'));

	    $confHear = new Zend_Form_Element_MultiCheckbox('conf_hear');
	    $confHear->setLabel('How did you hear about the conference? (check all that apply)')
				 ->setAttrib('class', 'tiny')
				 ->setMultiOptions(array(
					 'last' => 'During the last conference',
					 'pp' => 'Printed promotion',
					 'email' => 'Email',
					 'col' => 'Colleagues',
					 'web' => 'TERENA or NREN website',
					 'sns' => 'Social networking site (Facebook, Linkedin, Twitter...etc)',
					 'other' => 'Other'					 
				 ))
				 ->setDecorators(array('Composite'));

	    $hearOther = new Zend_Form_Element_Text('heard_other_spec');
	    $hearOther->setDescription('Other, please specify')
				  ->setAttrib('class', 'medium')
				  ->setDecorators(array('Composite'));

	    $beenBefore = new Zend_Form_Element_Radio('been_before');
	    $beenBefore->setLabel('Have you been to a TERENA conference before?')
				   ->setAttrib('class', 'tiny')
				   ->setMultiOptions(array(
					   'no' => 'No',
					   'yesone' => 'Yes, once',
					   'yestwice' => 'Yes, twice',
					   'yesthree' => 'Yes, three times and more'
				   ))
				   ->setDecorators(array('Composite'));

	    $comeAgain = new Zend_Form_Element_Radio('come_again');
	    $comeAgain->setLabel('Will you come to the TERENA conference again?')
				  ->setAttrib('class', 'tiny')
				  ->setMultiOptions(array(
					  'yes' => 'Yes, definitely',
					  'maybe' => 'Yes, maybe',
					  'un' => 'Undecided',
					  'probnot' => 'Probably not',
					  'no' => 'No'
				  ))
				  ->setDecorators(array('Composite'));

		$this->addElements(array(
			$id,
			$confRating,
			$partReasons,
			$partOther,
			$confHear,
			$hearOther,
			$beenBefore,
			$comeAgain
		));

	    $this->addElement('submit', 'submit', array(
			'decorators' => $this->_buttonElementDecorator
	    ));
	}

	/**
	 * Override method to unserialize multiCheckbox values
	 *
	 */
	public function setDefaults(array $defaults)
	{
		$defaults['part_reasons'] = (isset($defaults['part_reasons']) ) ? unserialize($defaults['part_reasons']) : null;
		$defaults['conf_hear'] = (isset($defaults['conf_hear']) ) ? unserialize($defaults['conf_hear']) : null;
		parent::setDefaults($defaults);
	}

}
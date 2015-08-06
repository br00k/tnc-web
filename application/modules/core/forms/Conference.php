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
 * @revision   $Id: Conference.php 41 2011-11-30 11:06:22Z gijtenbeek@terena.org $
 */

/**
 *
 * @package Core_Forms
 *
 * @note For advanced view options uncomment the __construct() method
 * and replace all Composite decoraters with _standardElementDecorator
 */
class Core_Form_Conference extends TA_Form_Abstract
{

	public function init()
	{
		$this->setAction('/core/conference/new');

		$name = new Zend_Form_Element_Text('name');
		$name->setLabel('Name')
			->setRequired(true)
			->addValidator('StringLength', true, array(2, 100,
						'messages' => array(
							Zend_Validate_StringLength::TOO_SHORT => 'Please provide a longer description',
							Zend_Validate_StringLength::TOO_LONG => 'Your description is too long'
							)
						))
			->setAttrib('class', 'large')
			->setDescription('Must be between 2 and 200 characters')
			->setDecorators(array('Composite'));

		$hostname = new Zend_Form_Element_Text('hostname');
		$hostname->setLabel('Hostname')
			->setRequired(true)
			->addValidator('regex', true, array(
						'pattern' => '/.*/',
						'messages' => array(Zend_Validate_Regex::NOT_MATCH => 'Wrong format')
						))
			->addValidator(new Zend_Validate_Db_NoRecordExists(array(
							'table' => 'conferences',
							'field' => 'hostname'
							)))
			->setAttrib('class', 'medium')
#->addFilter('StringtoLower')
			->setDescription('This is where CORE will be hosted from, must be a valid and existing hostname')
			->setDecorators(array('Composite'));

		$address = new Zend_Form_Element_Text('address');
		$address->setLabel('Venue address')
			->setRequired(false)
			->setAttrib('class', 'medium')
			->setDecorators(array('Composite'));

		$googleMapKey = new Zend_Form_Element_Text('googlemapskey');
		$googleMapKey->setLabel('Google maps API key')
			->setRequired(false)
			->addValidator('StringLength', true, array(86, 86,
						'messages' => array(
							Zend_Validate_StringLength::TOO_SHORT => 'Must be 86 characters long',
							Zend_Validate_StringLength::TOO_LONG => 'Must be 86 characters long'
							)
						))
			->setAttrib('class', 'medium')
			->setDescription('<a href="http://code.google.com/apis/maps/signup.html">Get one here</a>. This API key will be used to generate google maps for locations')
			->setDecorators(array('Composite'));

		$gcalUrl = new Zend_Form_Element_Text('gcal_url');
		$gcalUrl->setLabel('Google calendar event feed url')
			->setRequired(false)
			->addValidator('url')
			->addFilter('Null') // add this if you want to provide a blank value
			->setAttrib('class', 'medium')
			->setDescription('eg: https://www.google.com/calendar/feeds/tnc2011@terena.org/private/full - Used to synchronise sessions')
			->setDecorators(array('Composite'));

		$gcalUsername = new Zend_Form_Element_Text('gcal_username');
		$gcalUsername->setLabel('Google calendar username')
			->setRequired(false)
			->addFilter('Null') // add this if you want to provide a blank value
			->setAttrib('class', 'medium')
			->setDecorators(array('Composite'));

		$gcalPassword = new Zend_Form_Element_Text('gcal_password');
		$gcalPassword->setLabel('Google calendar password')
			->setRequired(false)
			->addFilter('Null') // add this if you want to provide a blank value
			->setAttrib('class', 'medium')
			->setDecorators(array('Composite'));

		$abbr = new Zend_Form_Element_Text('abbreviation');
		$abbr->setLabel('Abbreviation')
			->setRequired(true)
			->addValidator('regex', true, array(
						'pattern' => '/^[a-z0-9]+$/',
						'messages' => array(Zend_Validate_Regex::NOT_MATCH => 'Wrong format')
						))
			->addValidator(new Zend_Validate_Db_NoRecordExists(array(
							'table' => 'conferences',
							'field' => 'abbreviation'
							)))
			->setAttrib('class', 'medium')
			->setDescription('Used for prefixing filenames, email subjects, layout templates etc. Only lowercase and number allowed')
			->setDecorators(array('Composite'));

		$desc = new Zend_Form_Element_Textarea('description');
		$desc->setLabel('Description')
			->setAttrib('class', 'small')
			->setDescription('Must be between 5 and 500 characters')
			->setRequired(false)
			->addValidator('StringLength', true, array(5, 500,
						'messages' => array(
							Zend_Validate_StringLength::TOO_SHORT => 'Please provide a longer description',
							Zend_Validate_StringLength::TOO_LONG => 'Your description is too long'
							)
						))
			->setDecorators(array('Composite'));

		$streamUrl = new Zend_Form_Element_Text('stream_url');
		$streamUrl->setLabel('Stream url')
			->setRequired(false)
			->addValidator('url')
			->addFilter('Null') // add this if you want to provide a blank value
			->setAttrib('class', 'medium')
			->setDescription('For links to streaming video')
			->setDecorators(array('Composite'));

		$submitStart = new Zend_Form_Element_Text('submit_start');
		$submitStart->setLabel('Submit start')
			->setDescription('dd/mm/yyyy')
			->setAttrib('class', 'medium')
			->setRequired(false)
			->addFilter('Null')
			->setDecorators(array('Composite'));

		$submitEnd = new Zend_Form_Element_Text('submit_end');
		$submitEnd->setLabel('Submit end')
			->setDescription('dd/mm/yyyy')
			->setAttrib('class', 'medium')
			->setRequired(false)
			->addFilter('Null')
			->setDecorators(array('Composite'));

		$reviewStart = new Zend_Form_Element_Text('review_start');
		$reviewStart->setLabel('Review opens on')
			->setDescription('dd/mm/yyyy')
			->setAttrib('class', 'medium')
			->setRequired(false)
			->addFilter('Null')
			->setDecorators(array('Composite'));

		$reviewVis = new Zend_Form_Element_Text('review_visible');
		$reviewVis->setLabel('Reviewers can see all reviews on')
			->setDescription('dd/mm/yyyy')
			->setAttrib('class', 'medium')
			->setRequired(false)
			->addFilter('Null')
			->setDecorators(array('Composite'));

		$reviewEnd = new Zend_Form_Element_Text('review_end');
		$reviewEnd->setLabel('Review closes on')
			->setDescription('dd/mm/yyyy')
			->setAttrib('class', 'medium')
			->setRequired(false)
			->addFilter('Null')
			->setDecorators(array('Composite'));

		$feedbackEnd = new Zend_Form_Element_Text('feedback_end');
		$feedbackEnd->setLabel('Feedback closes on')
			->setDescription('dd/mm/yyyy')
			->setAttrib('class', 'medium')
			->setRequired(false)
			->addFilter('Null')
			->setDecorators(array('Composite'));

		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Administrator Email')
			->setRequired(true)
			->setAttrib('class', 'medium')
			->addValidators(array(
						array('EmailAddress', true)
						 ))
			->setDecorators(array('Composite'));

		$tz = new Zend_Form_Element_Text('timezone');
		$tz->setLabel('Timezone')
			->setRequired(true)
			->setAttrib('class', 'medium')
			->setValue(ini_get('date.timezone'))
			->setDecorators(array('Composite'));

		$layout = new Zend_Form_Element_Checkbox('layout');
		$layout->setLabel('Use a custom layout for this conference')
			->setRequired(false)
			->setDecorators(array('Composite'));

		$navigation = new Zend_Form_Element_Checkbox('navigation');
		$navigation->setLabel('Use a custom navigation for this conference')
			->setRequired(false)
			->setDecorators(array('Composite'));

		$this->addElements(array(
			$name,
			$hostname,
			$abbr,
			$email,
			$address,
			$googleMapKey,
			$gcalUrl,
			$gcalUsername,
			$gcalPassword,
			$desc,
			$streamUrl,
			//$tz,
			$submitStart,
			$submitEnd,
			$reviewStart,
			$reviewVis,
			$reviewEnd,
			$feedbackEnd,
			$layout,
			$navigation
		));

		$this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
		));

	}

#public function __construct($options = null)
#{
#   parent::__construct($options);
#
#   $this->setDecorators(array(array('ViewScript', array(
#       'viewScript' => 'conference/form-custom.phtml'
#   ))));
#
#}

}
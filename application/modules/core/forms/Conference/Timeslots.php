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
 * @revision   $Id: Timeslots.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */

/**
 * Timeslots form
 *
 * @note This form uses dynamic field functionality in combination with Composite Elements
 * @package Core_Forms 
 * @subpackage Core_Forms_Conference
 */
class Core_Form_Conference_Timeslots extends TA_Form_Abstract
{

	public function init()
	{
		parent::init();

		$this->setAction('/core/conference/timeslots');

		$conferenceId = new Zend_Form_Element_Hidden('conference_id');
		$conferenceId->setRequired(true)
					 ->addValidators(
					 	array('Int')
					 )
					 ->setDecorators($this->_hiddenElementDecorator);

		$timeslot = new TA_Form_Element_Timeslot('timeslot_1');
		$timeslot->clearDecorators()
				 ->addDecorator(new TA_Form_Decorator_Timeslot())
				 ->setIgnore(true) // don't include in values array
				 ->setAttrib('class', 'hidden'); // since this element will be used as a template, hide it!

		// add timeslot elements to subform so in isValid() I only have to loop over that form
		$subform = new Zend_Form_SubForm();
		$subform->setDecorators(array('FormElements'));

		$subform->addElements(array(
			$timeslot
		));
		$this->addSubForm($subform, 'dynamic');

		$this->addElements(array(
			$conferenceId
		));

		$this->addElement('button', 'add', array(
			'label' => 'Add new timeslot',
			'decorators' => $this->_buttonElementDecorator
		));

		$this->addElement('submit', 'submit', array(
			'label' => 'Submit',
			'decorators' => $this->_buttonElementDecorator
		));
	}

	/**
	 * Add dynamically added form elements to subform
	 *
	 * @param	Zend_Form_Element	$element
	 * @return	TA_Form_Element_Timeslot
	 */
	private function addDynamicElement($element, $values = null)
	{
		$timeslotsForm = $this->getSubForm('dynamic');
		if ($timeslotsForm->getElement($element)) {
			return;
		}
		$timeslot = new TA_Form_Element_Timeslot($element);
		$timeslot->clearDecorators()
				 ->addValidator(new TA_Form_Validator_Timeslot(), true)
				 ->addDecorator(new TA_Form_Decorator_Timeslot())
				 ->addDecorator('Errors', array('placement'=>'prepend'));

		$timeslotsForm->addElement($timeslot);

		return $timeslot;
	}

	/**
	 * Validate the form
	 * Override method to add form elements that were dynamically added
	 *
	 * @param  array $data
	 * @return boolean
	 */
	public function isValid($data)
	{
		foreach ($data['dynamic'] as $element => $values) {
			$this->addDynamicElement($element, $values);
		}

		return parent::isValid($data);
	}

	/**
	 * Set default values for elements
	 * Override method to add form elements that were dynamically added
	 * Every row in the dynamic value array adds the form element
	 *
     * @return Zend_Form
	 */
	public function setDefaults(array $values)
	{
		foreach ($values['timeslots'] as $dynamicValues) {
			if ($dynamicValues instanceof Core_Resource_Timeslot_Item) {
				$dynamicValues = $dynamicValues->toMagicArray('dd/MM/yyyy H:m');
			}

			$dynElement = $this->addDynamicElement('timeslot_'.$dynamicValues['timeslot_id']);
			if ($dynElement) {
				$dynElement->setValue($dynamicValues);
			}
		}
		parent::setDefaults($values);
	}

}
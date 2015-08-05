<?php
/**
 * Timeslots form
 *
 * This form uses dynamic field functionality in combination with Composite Elements
 *
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
	 */
	private function addDynamicElement($element, $values = null)
	{
		$timeslotsForm = $this->getSubForm('dynamic');
		if ( $timeslotsForm->getElement($element) ) {
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
	 * Override method to add form elements that were dynamically added
	 *
	 */
	public function isValid($data)
	{
		foreach ($data['dynamic'] as $element => $values) {
			$this->addDynamicElement($element, $values);
		}

		return parent::isValid($data);
	}

	/**
	 * Override method to add form elements that were dynamically added
	 * Every row in the dynamic value array adds the form element
	 *
	 */
	public function setDefaults(array $values)
	{
		foreach ($values['timeslots'] as $dynamicValues) {
			if ($dynamicValues instanceof Core_Resource_Timeslot_Item) {
				$dynamicValues = $dynamicValues->toMagicArray('dd/MM/yyyy H:m');
			}

			$this->addDynamicElement('timeslot_'.$dynamicValues['timeslot_id'])
				 ->setValue($dynamicValues);
		}
		parent::setDefaults($values);
	}

}
<?php

class Core_Form_Conference_Edit extends Core_Form_Conference
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/conference/edit');
	    $this->addElement('hidden', 'conference_id', array(
			'validators' => array(
				array('Int')
			),
			'required' => true,
			'decorators' => $this->_hiddenElementDecorator
	    ));
	}

	/**
	 * Override isValid to add 'exclude' option to db validators.
	 * I chose to exclude based on conference_id instead of the
	 * actual field because that incurs less overhead.
	 *
	 */
	public function isValid($data)
	{
		$this->getElement('abbreviation')
		     ->getValidator('Zend_Validate_Db_NoRecordExists')
		     ->setExclude(array(
		        'field' => 'conference_id',
		        'value' => $data['conference_id']
		     ));

		$this->getElement('hostname')
		     ->getValidator('Zend_Validate_Db_NoRecordExists')
		     ->setExclude(array(
		        'field' => 'conference_id',
		        'value' => $data['conference_id']
		     ));

		return parent::isValid($data);
	}
}
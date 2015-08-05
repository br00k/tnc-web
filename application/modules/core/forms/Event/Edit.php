<?php

class Core_Form_Event_Edit extends Core_Form_Event
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/event/edit');
	    $this->addElement('hidden', 'event_id', array(
			'validators' => array(
				array('Int')
			),
			'required' => true,
			'decorators' => $this->_hiddenElementDecorator
	    ));

	}

}
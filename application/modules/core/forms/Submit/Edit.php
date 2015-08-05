<?php

class Core_Form_Submit_Edit extends Core_Form_Submit
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/submit/edit');

	    $submissionId = new Zend_Form_Element_Hidden('submission_id');
	    $submissionId->setRequired(true)
	    			 ->setLabel('submission_id')
	    			 ->addValidators(
	    			 	array('Int')
	    			 )
	    			 ->setDecorators(array('Composite'));

		$submission = $this->getSubForm('submission');

		$submission->addElement($submissionId);
		
		$file = $submission->getElement('file');
		$file->setDescription($file->getDescription(). ' (Uploading a new file will overwrite the old one)')
			 ->setRequired(false);

	}


}
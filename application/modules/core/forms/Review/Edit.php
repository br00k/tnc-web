<?php

class Core_Form_Review_Edit extends Core_Form_Review
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/review/edit');

	    $reviewId = new Zend_Form_Element_Hidden('review_id');
	    $reviewId->setRequired(true)
	    		 ->setLabel('review_id')
	    		 ->addValidators(
	    		    array('Int')
	    		 )
	    		 ->setDecorators(array('Composite'));
	    
		$this->addElement($reviewId);    
	    
	}


}
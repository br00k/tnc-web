<?php

class Core_Form_Feedback_Programme extends TA_Form_Abstract
{
	public function init()
	{
		parent::init();

		$this->setAction('/core/feedback/programme');

	    $id = new Zend_Form_Element_Hidden('id');
	    $id->setRequired(true)
	       ->setLabel('id')
		   ->addValidators(
			   array('Int')
		   )
		   ->setDecorators(array('Composite'));

	    $best = new Zend_Form_Element_Textarea('best_stuff');
	    $best->setLabel('Which sessions or presentations were the best – and why?')
	    	 	 ->setAttrib('class', 'medium')
	    	 	 ->setDescription('Please limit your comments to 1000 characters')
	    	 	 ->setRequired(false)
	    	 	 ->addValidator('StringLength', true, array(1, 5000,
	    	 	 	'messages' => array(
			 	 		Zend_Validate_StringLength::TOO_SHORT => 'Please provide a longer comment',
			 	 		Zend_Validate_StringLength::TOO_LONG => 'Your comment is too long'
			 	 	)
	    	 	 ))
			 	 ->setDecorators(array('Composite'));

		$worst = clone($best);
		$worst->setName('worst_stuff')
			  ->setLabel('Which sessions or presentations were the worst – and why?');
			  
		$comments = clone($best);
		$comments->setName('comments')
				 ->setLabel('Comments on the programme');

		$this->addElements(array(
			$id,
			$best,
			$worst
		));

		$elements = array(
			'exhibition' => 'How useful did you find the exhibitions and demos?',
			'meetings' => 'How useful did you find the meetings / workshops around the conference?',
			'lightning' => 'How useful did you find the lightning talks?',
			'poster' => 'How useful did you find the poster presentations?'
		);

		// add all elements in loop, since they are all the same
		foreach ($elements as $name => $label) {
	    	$newSelect = new Zend_Form_Element_Radio($name);
	    	$newSelect->setLabel($label)
					  ->setAttrib('class', 'tiny')
					  ->setMultiOptions($this->_getFieldValues('rating', 'feedback'))
					  ->setDecorators(array('Composite'));					  

	    	$newText = new Zend_Form_Element_Text('remarks_'.$name);
	    	$newText->setDescription('Comments')
					->setAttrib('class', 'medium')
					->setDecorators(array('Composite'));

			$this->addElements(array($newSelect, $newText));
		}
		
		$this->addElement($comments);

	    $this->addElement('submit', 'submit', array(
			'decorators' => $this->_buttonElementDecorator
	    ));
	}


}
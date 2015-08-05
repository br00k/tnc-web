<?php

class TA_Form_Abstract extends Zend_Form
{

	protected $_model;

	protected $_standardElementDecorator = array(
		'ViewHelper',
		array('Label', array('class'=>'desc')),
		'Errors',
		array('HtmlTag', array('tag'=>'li'))
	);

	protected $_fileElementDecorator = array(
		'File',
		array('Label', array('tag'=>'li', 'class'=>'desc')),
		array('HtmlTag', array('tag'=>'li')),
		// do not escape HTML chars in description for links
		array('Description', array('tag' => 'div', 'class' => 'description', 'escape' => false))
	);

	protected $_magicFileElementDecorator = array(
		'File',
		array('Label', array('tag'=>'li', 'class'=>'desc')),
		array('HtmlTag', array('tag'=>'li')),
		// do not escape HTML chars in description for links
		array('Description', array('tag' => 'div', 'class' => 'description', 'escape' => false)),
		'MagicFile'
	);

	protected $_buttonElementDecorator = array(
		'ViewHelper',
		array('HtmlTag', array('tag'=>'li', 'class'=>'button'))
	);

	protected $_checkboxElementDecorator = array(
		array('HtmlTag', array('tag'=>'li')),
		array('Label', array('escape'=>false, 'placement' => 'APPEND', 'class' => 'choice'))
	);

	protected $_standardGroupDecorator = array(
	    'FormElements',
	    array('HtmlTag', array('tag'=>'ol')),
	    'Fieldset'
	);

	protected $_hiddenElementDecorator = array(
		'ViewHelper',
		array('HtmlTag', array('tag'=>'li', 'class'=>'hidden'))
	);

	protected $_buttonGroupDecorator = array(
		'FormElements',
	    'Fieldset'
	);

	/**
	 * @todo add subform decorator
	 */
	public function __construct($options = null)
	{
		parent::__construct($options);

		// add custom decorator plugins
		$this->addElementPrefixPath('TA_Form_Decorator',
		                     		'TA/Form/Decorator/',
		                    		'decorator');

		// add custom validator plugins
		$this->addElementPrefixPath('TA_Form_Validator',
		                     		'TA/Form/Validator/',
		                    		'validate');

		$this->setAttrib('accept-charset', 'UTF-8');

		$this->setDecorators(array(
		   'FormElements',
		    array('HtmlTag', array('tag'=>'ol')),
		   'Form'
		));

		// Strip whitespace from all elements
		$this->addElementFilters(array('StringTrim', new TA_Filter_HTMLPurifier()));
	}


	public function addElementFilters(array $filters)
    {
        foreach ($this->getElements() as $element) {
        	if (!$element->getFilter('Null')) {
            	$element->addFilters($filters);
            }
        }
        return $this;
    }

    /**
     * Return form values defined in config for use in select fields
     *
     * @param 	string	$field		Fieldname to get values from
     * @param	string	$form
     * @return array
     */
    protected function _getFieldValues($field, $form = null)
    {
    	// if $form is not given, get formname from calling class
    	if (!$form) {
			$match = preg_match('/.*?Form_(.*)/i', get_class($this), $matches);
			if ($exploded = explode('_', $matches[1]) ) {
				$form = $exploded[0];
			} else {
				$form = $matches[1];
			}
			$form = strtolower($form);
    	}
		if (!Zend_Registry::isRegistered('formconfig')) {
    		$formConfig = new Zend_Config(require APPLICATION_PATH.'/configs/formdefaults.php');
			Zend_Registry::set('formconfig', $formConfig);
		}
    	return Zend_Registry::get('formconfig')->formdefaults->$form->$field->toArray();
    }

}
<?php

class Core_View_Helper_GetFormValue extends Zend_View_Helper_Abstract
{

	/**
	 * Translates a db value to a form value
	 *
	 * @param	string	$field	Fieldname (key)
	 * @param	string	$dbValue	Value of field
	 * @param	string	$form		Form key to get value from
	 */
	public function getFormValue($field, $dbValue, $form)
	{
		if (!Zend_Registry::isRegistered('formconfig')) {
    		$formConfig = new Zend_Config(require APPLICATION_PATH.'/configs/formdefaults.php');
			Zend_Registry::set('formconfig', $formConfig);
		}
		return Zend_Registry::get('formconfig')->formdefaults->$form->$field->get($dbValue);

	}

}
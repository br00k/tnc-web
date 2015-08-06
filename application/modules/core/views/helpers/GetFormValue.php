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
 * @revision   $Id: GetFormValue.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */

/**
 * Form values View helper
 *
 * @package Core_View_Helper
 */
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
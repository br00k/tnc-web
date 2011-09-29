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
 * @revision   $Id: Edit.php 619 2011-09-29 11:20:22Z gijtenbeek $
 */
 
/** 
 *
 * @package Core_Forms
 * @subpackage Core_Forms_Conference
 */
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
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
 * @revision   $Id: Feedbackpresentations.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */

/** 
 *
 * @package Core_Resource
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Feedbackposters extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'feedback.posters';
	
	protected $_primary = array('id');

	protected $_rowClass = 'Core_Resource_Feedback_Item';

	public function init() {}

	/**
	 * Gets feedback by feedback id
	 * @return object Zend_Db_Table_Row
	 */
	public function getFeedbackByCodeId($codeId)
	{
		return $this->find($codeId)->current();
	}
	
}
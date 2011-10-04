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
 * @revision   $Id: MagicFile.php 598 2011-09-15 20:55:32Z visser $
 */

/**
 * Custom Form Element for Files.
 * This element adds the method 'setTaFile' to the form element
 * the file will be rendered by the decorator
 *
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 * @package TA_Form
 * @subpackage Element
 * @see TA_Form_Decorator_MagicFile
 */
class TA_Form_Element_MagicFile extends Zend_Form_Element_File
{

	/**
	 * Holds the file object
	 * @var	Core_Resource_File_Item
	 */
	protected $_taFile;

	/**
	 * Set file property for later access by decorator
	 *
	 * @param	Core_Resource_File_Item		$file
	 * @return	TA_Form_Element_MagicFile	To allow for ...
	 */
	public function setTaFile(Core_Resource_File_Item $file)
	{
		$this->_taFile = $file;
		return $this;
	}

	/**
	 * Get file object
	 *
	 * @return	Core_Resource_File_Item
	 */
	public function getTaFile()
	{
		return $this->_taFile;
	}

}
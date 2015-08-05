<?php
/**
 * Custom Form Element for Files.
 * This element adds the method 'setTaFile' to the form element
 * the file will be rendered by the decorator
 *
 * @see TA_Form_Decorator_MagicFile
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
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
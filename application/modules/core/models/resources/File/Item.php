<?php
/**
	* This class represents a single record
	* Methods in this class could include logic for a single record
	* for example sticking first_name and last_name together in a getName() method
	*
	*/
class Core_Resource_File_Item extends TA_Model_Resource_Db_Table_Row_Abstract
{
	public function __construct($config = array()) {
		parent::__construct($config);
		// strip filename_orig from weird characters and transform underscores to camelcase
		$pattern = '/(.*[\\\\\/])?(.*)$/';
		preg_match($pattern, $this->filename_orig, $matches);
		$sanatized = str_replace(array(' ', ':','*','?','"', '<', '>', '|'),'_',$matches[2]);

		$inflector = new Zend_Filter_Inflector(':name');

		$inflector->setRules(array(
		    ':name'  => array('Word_UnderscoreToCamelCase')
		));

		$filtered = $inflector->filter(array('name' => $sanatized));

		$this->filename_orig = $filtered;
	}
	
	/**
	 * Gets thumbnail if there is one
	 *
	 * @return	mixed	false if there is no thumbnail or the fullpath
	 * 					and name if there is.
	 */
	public function getThumb()
	{
		$resize = new TA_Filter_ImageResize();
		$thumbName = $this->filename.$resize->getThumbnailSuffix();
		if (file_exists(Zend_Registry::get('config')->directories->uploads.$thumbName)) {
			return $thumbName;
		}
		return false;
	}
	
	/**
	 * Get normalized filename
	 * @return string
	 */
	public function getNormalizedName()
	{
		$conference = Zend_Registry::get('conference');
		return $conference['abbreviation'].'_'.$this->core_filetype.'_'.$this->filename_orig;	
	}
	
	/**
	 * Get filename prefixed with file id
	 * @return string
	 */
	public function getIndexedName()
	{
		return $this->file_id . '_' . $this->filename_orig;
	}
	
	/**
	 * Get full filepath
	 * @return string
	 */
	public function getFullFilePath()
	{
		return Zend_Registry::get('config')->directories->uploads.$this->filename;
	}
	
}
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
 * @revision   $Id$
 */
require_once dirname(dirname(__FILE__)).'/phpthumb/ThumbLib.inc.php';

/**
 * Resize an image
 *
 * @author Christian Gijtenbeek
 * @package TA_Filter
 */ 
class TA_Filter_ImageResize implements Zend_Filter_Interface
{
     /**
     * Default output width.
     * @var int
     */
    const DEFAULT_WIDTH = 32;

    /**
     * Default output height.
     * @var int
     */
    const DEFAULT_HEIGHT = 32;

    /**
     * Default output height.
     * @var int
     */
    const DEFAULT_SUFFIX = '-thumb';

    /**
     * Default output path.
     * @var string
     */
    const DEFAULT_OUTPUTPATH = './';

    /**
     * Default resize method.
     * @var string
     */
    const DEFAULT_METHOD = 'adaptiveResize';

    /**
     * Width of output image.
     * @var integer
     */
    protected $_width = self::DEFAULT_WIDTH;

    /**
     * Height of output image.
     * @var integer
     */
    protected $_height = self::DEFAULT_HEIGHT;

    /**
     * Path to write the files to
     * @var string
     */
    protected $_outputPath = self::DEFAULT_OUTPUTPATH;

    /**
     * String to append to filename
     * @var string
     */
    protected $_thumbnailSuffix = self::DEFAULT_SUFFIX;

    /**
     * Method to use for resizing
     * @var string
     */
    protected $_resizeMethod = self::DEFAULT_METHOD;

	/**
	 *
	 *
	 */
	public function filter($value)
	{
        if (!extension_loaded('gd')) {
        	throw new Zend_Exception('GD extension is not available. Can\'t process image.');
        }

        if(!file_exists($value)) {
            throw new Zend_Exception('Image does not exist: ' . $value);
        }

		$resized = $this->_resize($value);
		$this->_writeResized($resized);
		return $value;
	}

    protected function _resize($filename)
    {
        $thumb = PhpThumbFactory::create($filename);
        $this->setOutputPath($filename.$this->getThumbnailSuffix());
		return $thumb->{$this->_resizeMethod}($this->getWidth(), $this->getHeight());
    }

    protected function _writeResized(GdThumb $resized, $type = null)
    {
    	$resized->save($this->getOutputPath(), $type);
    	return $this->getOutputPath();
    }

    /**
     * Set the resize method.
     * @param string $method Defined by PHPThumb:
     * 						'resize', 'adaptiveResize', 'cropFromCenter', 'crop'
     * @return TA_Filter_ImageResize Fluent interface
     */
    public function setResizeMethod($method)
    {
        $this->_resizeMethod = $method;
        return $this;
    }

    /**
     * Get the resize method
     * @return string
     */
    public function getResizeMethod()
    {
        return $this->_resizeMethod;
    }

    /**
     * Get the output width in pixels.
     * @return int
     */
    public function getWidth()
    {
        return $this->_width;
    }

    /**
     * Set the output width in pixels.
     * @param int $width
     * @return TA_Filter_ImageResize Fluent interface
     */
    public function setWidth($width)
    {
        $this->_width = $width > 0 ? $width : 1;
        return $this;
    }

    /**
     * Set the output height in pixels.
     * @param int $height
     * @return TA_Filter_ImageResize Fluent interface
     */
    public function setHeight($height)
    {
        $this->_height = $height > 0 ? $height : 1;
        return $this;
    }

    /**
     * Get the output height in pixels.
     * @return int
     */
    public function getHeight()
    {
        return $this->_height;
    }

    /**
     * Gets the thumbnail suffix
     * @return string
     */
    public function getThumbnailSuffix()
    {
    	return $this->_thumbnailSuffix;
    }

    /**
     * Set the thumbnail suffix
     * @param string $suffix
     * @return TA_Filter_ImageResize Fluent interface
     */
    public function setThumbnailSuffix($suffix)
    {
    	return $this->_thumbnailSuffix = $suffix;
    	return $this;
    }

    /**
     * Set output path
     * @param string $path
     * @return TA_Filter_ImageResize Fluent interface
     */
    public function setOutputPath($path)
    {
    	// add some checks here to see if $path exists
    	$this->_outputPath = $path;
    	return $this;
    }

    /**
     * Get the output path
     * @return string
     */
    public function getOutputPath()
	{
		return $this->_outputPath;
	}

}

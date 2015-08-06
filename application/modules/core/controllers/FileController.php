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
 * @revision   $Id: FileController.php 25 2011-10-04 20:46:05Z visser@terena.org $
 */
 
/**
 * FileController
 *
 * @package Core_Controllers
 * @todo deleting presentation from session redirects wrong
 */  
class Core_FileController extends Zend_Controller_Action
{
	
    /**
     * File model
     *
     * @var Core_Model_File
     */
	protected $_fileModel;

	public function init()
	{
		$this->_fileModel = new Core_Model_File();
		Zend_Controller_Action_HelperBroker::addHelper(new TA_Controller_Action_Helper_SendFile());
    }

    /**
	 * Get file details from the database and return the file as a download
	 *
	 * @return	void
     */
    public function getfileAction()
    {
    	$file = $this->_fileModel->getFileById($this->getRequest()->getParam('id'));

		// not necessary but more pretty
		#$filter = new Zend_Filter_RealPath();
		#$filtered = $filter->filter($file->filename);

		$filePath = Zend_Registry::get('config')->directories->uploads.$file->filename;

    	$this->_helper->SendFile($filePath, $file->mimetype, array('filename' => $file->getNormalizedName()));
    	exit();
    }

    /**
     * Get a file from the filesystem and return it as a download
     *
     * @return	void
     */
    public function getstaticfileAction()
    {
    	$file = $this->getRequest()->getParam('file');
    	$type = $this->getRequest()->getParam('type');

    	if (!$file) {
    		throw new Exception("required parameter 'file' not found");
    	}

    	switch ($type) {
    		case 'mail':
    			$file = APPLICATION_PATH.'/../data/mails/'.$file;
    			$mimetype = 'message/rfc822';
    		break;
    		case 'zip':
    			$file = Zend_Registry::get('config')->directories->uploads.$file;
    			$mimetype = 'application/zip';
    		break;
    	}

    	$this->_helper->SendFile($file, $mimetype);
    	exit();
    }

    /**
     * Display a file inline. If there is a thumbnail it picks this over the normal file
     *
     * @return	void
     */
	public function showAction()
	{
    	$file = $this->_fileModel->getFileById($this->getRequest()->getParam('id'));

    	$filename = $file->filename;

    	if ($file->getThumb()) {
    		$filename = $file->getThumb();
    	}
    	$this->_helper->SendFile(Zend_Registry::get('config')->directories->uploads.$filename, $file->mimetype, array('disposition' => 'inline'));
    	exit();
	}

}


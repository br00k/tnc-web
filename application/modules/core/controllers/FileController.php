<?php

class Core_FileController extends Zend_Controller_Action
{

	/**
	 *  @todo: deleting presentation from session redirects wrong
	 *
	 */
	protected $_fileModel;

	/**
	 *
	 *
	 */
	public function init()
	{
		$this->_fileModel = new Core_Model_File();
		Zend_Controller_Action_HelperBroker::addHelper(new TA_Controller_Action_Helper_SendFile());
    }

    /**
	 * Get a file from the database and return it as a download
	 *
     */
    public function getfileAction()
    {
    	$file = $this->_fileModel->getFileById( $this->getRequest()->getParam('id') );

		// not necessary but more pretty
		#$filter = new Zend_Filter_RealPath();
		#$filtered = $filter->filter($file->filename);

		$filePath = Zend_Registry::get('config')->directories->uploads.$file->filename;

    	$this->_helper->SendFile($filePath, $file->mimetype, array('filename' => $file->getNormalizedName() ));
    	exit();
    }

    /**
     * Get a file from the filesystem and return it as a download
     *
     * @param	string	$file	The file to retrieve
     * @param	string	$type	The type of file you are retrieving
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
    			$file = APPLICATION_PATH . '/../data/mails/'.$file;
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
     * @return void
     */
	public function showAction()
	{
    	$file = $this->_fileModel->getFileById( $this->getRequest()->getParam('id') );

    	$filename = $file->filename;

    	if ($file->getThumb()) {
    		$filename = $file->getThumb();
    	}
    	$this->_helper->SendFile(Zend_Registry::get('config')->directories->uploads.$filename, $file->mimetype, array('disposition' => 'inline'));
    	exit();
	}

}


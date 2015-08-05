<?php

class TA_Controller_Plugin_MemoryPeakUsageLog extends Zend_Controller_Plugin_Abstract
{
	protected $_logger = null;

	public function __construct(Zend_Log $logger)
	{
		$this->_logger = $logger;
	}

	public function dispatchLoopShutdown()
	{
		$peakUsage = memory_get_peak_usage(true);
		$url = $this->getRequest()->getRequestUri();		
		$this->_logger->info($peakUsage . ' bytes ' . $url);
	}

}
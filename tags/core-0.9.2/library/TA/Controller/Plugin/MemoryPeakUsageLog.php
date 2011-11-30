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

/**
 * Show memory peaks
 *
 * @author Christian Gijtenbeek
 * @package TA_Controller
 * @subpackage Plugin
 */
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
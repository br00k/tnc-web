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
	 * @revision   $Id: TimeUntil.php 33 2011-10-13 10:33:44Z gijtenbeek@terena.org $
	 */
/**
	 * Zym Framework
	 *
	 * LICENSE
	 *
	 * This source file is subject to the new BSD license that is bundled
	 * with this package in the file LICENSE.txt.
	 *
	 * @category   Zym
	 * @package    Zym_View
	 * @subpackage Helper
	 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
	 * @license    http://www.zym-project.com/license New BSD License
	 */

/**
 * Calculates time until a certain event returning a phrase similar to "5 weeks ago"
 *
 * @author     Geoffrey Tran
 * @license    http://www.zym-project.com/license New BSD License
 * @package    Zym_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 */
class Zend_View_Helper_TimeUntil extends Zend_View_Helper_Abstract
{
	/**
	 * Time chunks in seconds => string format
	 *
	 * Order is FIFO largest to smallest
	 *
	 * @var array
	 */
	protected $_dateChucks = array(
		31536000 => 'year',
		2592000  => 'month',
		604800   => 'week',
		86400    => 'day',
		3600     => 'hour',
		60       => 'minute',
		1        => 'second'
	);

	/**
	 * Calculates time until a certain event returning a phrase similar to "5 weeks ago"
	 *
	 * @param	mixed integer or Zend_Date object		$timestamp
	 * @param	integer		$time	Timestamp to use instead of time()
	 */
	public function timeUntil($timestamp, $time = null)    
	{
		if ($timestamp instanceof Zend_Date) {
			$timestamp = $timestamp->getTimestamp();
		}
		$output     = '';
		$translator = $this->view->getHelper('translate');

		if ($time === null) {
			$time = time();
		}

		// Seconds since
		$since = $timestamp - $time;

		foreach ($this->_dateChucks as $seconds => $name) {
			if (!isset($largestChunk)) {
				if (!isset($largestChunk)) {
					$ratio = $since / $seconds;
					$chunk = ($since < 0) ? -floor(abs($ratio)) : floor($ratio);
				}
			}

			// Compute chunks
			if (isset($chunk) && $chunk != 0 && !isset($largestChunk)) {
				$largestChunk        = $chunk;
				$largestChunkName    = ($chunk == 1) ? $name : $name.'s';
				$largestChunkSeconds = $seconds;
			} else if (isset($chunk) && $chunk == 0 && !isset($largestChunk)) {
				// Handle if it 0 seconds
				$output = $translator->translate('less than a second');
			} else if (isset($largestChunk)) {
				$ratio = ($since - ($largestChunkSeconds * $largestChunk)) / $seconds;
				$chunk = ($since < 0) ? -floor(abs($ratio)) : floor($ratio);

				if ($chunk != 0) {
					$secondChunk     = $chunk;
					$secondChunkName = ($chunk == 1) ? $name : $name.'s';
				}

				break;
			}
		}

		if ($translator->getTranslator() === null) {
			if (isset($secondChunk)) {
				$output = sprintf("%d $largestChunkName and %d $secondChunkName", $largestChunk, $secondChunk);
			} else if (isset($largestChunk)) {
				$output = sprintf("%d $largestChunkName", $largestChunk);
			}
		} else {
			if (isset($secondChunk)) {
				$output = $translator->translate("%d $largestChunkName and %d $secondChunkName", $largestChunk, $secondChunk);
			} else if (isset($largestChunk)) {
				$output = $translator->translate("%d $largestChunkName", $largestChunk);
			}
		}

		return $output;
	}
}
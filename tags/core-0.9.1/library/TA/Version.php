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
 * @revision   $Id: Version.php 598 2011-09-15 20:55:32Z visser $
 */

final class TA_Version {
	/**
	 * CORE version identification - see compareVersion()
	 */
	const VERSION = '0.9dev';

	/**
	 * Compare the specified version string $version with the
	 * current TA_Version::VERSION of CORE.
	 *
	 * @param  string  $version  A version string (e.g. "0.7.1").
	 * @return int           -1 if the $version is older,
	 *                           0 if they are the same,
	 *                           and +1 if $version is newer.
	 *
	 */
	public static function compareVersion($version) {
		$version = strtolower($version);
		return version_compare($version, strtolower(self::VERSION));
	}
}

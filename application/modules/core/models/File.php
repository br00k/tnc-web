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
 * File Model
 *
 * @package Core_Model
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Model_File extends TA_Model_Acl_Abstract
{

	/**
	 * Get file by id
	 *
	 * @param		integer		$id		User id
	 * @return		Core_Resource_File_Item
	 */
	public function getFileById($id)
	{
		$row = $this->getResource('filesview')->getFileById( (int) $id );
    	if ($row === null) {
    		throw new TA_Model_Exception('id not found');
    	}

		// if file is submission and user is not allowed to get the specific type
    	if ( ($row->core_filetype == 'submission') && (!$this->checkAcl('getsubmission')) ) {
			throw new TA_Model_Exception('Insufficient rights for downloading this type of file');
    	}
    	return $row;
	}

}
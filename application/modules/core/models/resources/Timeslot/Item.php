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
 * @revision   $Id: Item.php 28 2011-10-05 12:12:04Z gijtenbeek@terena.org $
 */

/**
 * Timeslot row
 *
 * @package Core_Resource
 * @subpackage Core_Resource_Timeslot
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Timeslot_Item extends TA_Model_Resource_Db_Table_Row_Abstract
{
	// returns concatenated start/end time, eg: "10/11/2010 09:00-11:00"
	public function getCompleteTimeslot()
	{
		return $this->_isoToNormalDate($this->tstart, 'dd/MM/yyyy HH:mm') . '-' . $this->_isoToNormalDate($this->tend, 'HH:mm');
	}
	
	public function getTimeslotTimes()
	{
		return $this->_isoToNormalDate($this->tstart, 'HH:mm') . '-' . $this->_isoToNormalDate($this->tend, 'HH:mm');
	}
	
	public function getStartDay()
	{
		return $this->_isoToNormalDate($this->tstart, Zend_Date::DATE_SHORT);
	}
}
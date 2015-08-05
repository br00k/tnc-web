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
 * @revision   $Id: Set.php 41 2011-11-30 11:06:22Z gijtenbeek@terena.org $
 */

/**
 * Review rowset
 *
 * @package Core_Resource
 * @subpackage Core_Resource_Review
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Resource_Review_Set extends Zend_Db_Table_Rowset_Abstract
{

	private $_e;

	/**
	 * Calculate tiebreaker
	 *
	 * @return mixed null if there is no tiebreaker, or difference between e value and threshold
	 */
	public function getTieBreaker()
	{
		$config = Zend_Registry::get('config');
		if (($e = $this->getEvalue()) > $config->core->review->tiebreaker) {
			return $e - $config->core->review->tiebreaker;
		}
		return null;
	}

	/**
	 * Lazy load e value
	 *
	 * @return float
	 */
	public function getEvalue()
	{
		if (!$this->_e) {
			$this->_e = $this->_getEscore();
		}
		return $this->_e;
	}

	/**
	 * Calculate E Value
	 *
	 * E is higher if two people of equal experience disagree than if two people of disparate experience disagree.
	 * For fixed values of experience, E increases as the disagreement in ratings increases.
	 *
	 * @return	float
	 */
	private function _getEScore()
	{
		if ($this->count() > 1) {
			$w = array();
			$r = array();
			// calc tiebreaker
			foreach ($this as $review) {
			    $w[] = $review->self_assessment;
			    $r[] = $review->rating;
			}
			return ( $w[0] * $w[1] * pow($r[0]-$r[1],2) ) / ( 4 * pow($w[0] + $w[1],2) );
		}
		return 0;
	}

}
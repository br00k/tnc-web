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
 * @revision   $Id: User.php 37 2011-10-18 14:09:56Z gijtenbeek@terena.org $
 */

/**
 * Custom User Reviewer form element decorator
 *
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 * @package TA_Form
 * @subpackage Decorator
 */
class TA_Form_Decorator_Userreviewer extends TA_Form_Decorator_User
{

	/**
	 * Adds custom action to renderer
	 * 
	 * @param	$user		Core_Resource_User_Item
	 * @param	$linkedIds	Array of foreign table keys mapping to user_ids
	 */
	protected function getCustomTaAction(Core_Resource_User_Item $user, array $linkedIds)
	{
		$tiebreaker = $user->isReviewTiebreaker($linkedIds[$user->user_id]);
		$title = ($tiebreaker) ? 'Remove this reviewers tiebreaker status' : 'Make this reviewer a tiebreaker';
		$class = ($tiebreaker) ? '' : 'inactive';
		return '<a class="'.$class.'" title="'.($title).'" href="'
    	    	.$this->getElement()->getView()->url(array(
		    		'controller' => $this->getElement()->getTaController(),
		    		'action' => 'toggletiebreaker',
		    		'id' => $linkedIds[$user->user_id],
		    		'value' => ($tiebreaker) ? false : true,
		    	), 'main-module') .'">tiebreaker</a>';
	}

    public function render($content)
    {
    	// show custom action
    	$this->setOption('showCustomTaAction', true);
    	return parent::render($content);
    }

}
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
 * AbstractController
 *
 * @package Rest_Controller
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
abstract class Rest_AbstractController extends Zend_Rest_Controller
{
	protected $_sharedViews = false;

	/**
	 * All controllers will use the views that are stored in
	 * modules/rest/views/scripts/_shared/
	 *
	 * This method is useful if the same actions in
	 * different controllers share the same views
	 *
	 * This method can be used to control the shared views
	 * from the controller
	 *
	 * @param boolean $flag
	 */
	protected function enableSharedViews($flag=true)
	{
		if($flag) {
			$this->_helper->viewRenderer->setViewScriptPathSpec('_shared/:action.:suffix');
		}
		else {
			$this->_helper->viewRenderer->setViewScriptPathSpec(':action.:suffix');
		}
	}

	public function init()
	{
		$this->_helper->contextSwitch()
			 ->addActionContext('get', array('xml','json'))
			 ->addActionContext('post',  array('xml','json'))
			 ->initContext();
		$this->_helper->layout()->disableLayout();

		// can be used to set the shared views globally
		if ($this->_sharedViews) {
			$this->enableSharedViews(true);
		}
	}

	public function indexAction()
    {
    	$this->_forward('get');
    }
}
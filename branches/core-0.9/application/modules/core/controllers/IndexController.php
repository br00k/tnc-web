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
 * @revision   $Id: IndexController.php 619 2011-09-29 11:20:22Z gijtenbeek $
 */

/**
 * IndexController
 *
 * @package Core_Controllers
 */
class Core_IndexController extends Zend_Controller_Action
{

	public function init()
	{
    	$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}

    public function indexAction()
    {
    	$this->view->threeColumnLayout = true;
		$this->view->headScript()->appendFile('/js/jwplayer.js');
	}

}


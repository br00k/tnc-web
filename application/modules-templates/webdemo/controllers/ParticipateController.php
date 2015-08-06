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
	 * @revision   $Id: ParticipateController.php 25 2011-10-04 20:46:05Z visser@terena.org $
	 */
require_once APPLICATION_PATH.'/modules/webdemo/controllers/AbstractController.php';

class Webdemo_ParticipateController extends Webdemo_AbstractController
{

	public function indexAction()
	{
	}

	public function participantsAction()
	{
		$db = Zend_Db::factory(Zend_Registry::get('webConfig')->resources->multidb->webshop);
		$query = "select fname, lname, org from vw_prodpart 
		where product_id IN (57,58,59) and order_status NOT IN ('canceled', 'pending', 'refund') AND email !='cwright@csir.co.za' order by lname";
		
		$this->view->participants = $db->query($query)->fetchAll();
	}
}


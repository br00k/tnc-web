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
require_once APPLICATION_PATH.'/modules/webdemo/controllers/AbstractController.php';

class Webdemo_VenueController extends Webdemo_AbstractController
{

    public function indexAction()
    {
	}

	public function cancellationAction()
	{
		$this->view->threeColumnLayout = true;
	}

	public function toursAction()
	{
		$this->view->threeColumnLayout = true;
	}

	public function mapAction()
	{
		//$this->view->headScript()->appendFile('/javascript/mootools/mootools-latest.js');
		$this->_includeGoogleMap();
	}

	public function hotelsAction()
	{
		$this->_includeGoogleMap();
	}

	/**
	 * @todo: replace hardcoded google maps key with configured value
	 * This is easily accessible via the conference action helper
	 *
	 */
	private function _includeGoogleMap()
	{
		$this->view->headScript()->appendFile('/includes/tnc2011/js/venue.js');
		$this->view->headScript()->appendFile('http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=ABQIAAAAVDRAx-g4X7P_QMPTVZWw9xTFqjhVLAxw9jSR0CnodnK3Y0eDmBSTTq6sZ48MteDEd1SZukPuPMoQrQ');
	}
}
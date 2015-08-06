<?php
/**
 *
 * @todo find out why the dispatcher check does not work
 * @todo set the db resource in the registry so I can get it from there
 */
class Application_Plugin_Conditionals extends Zend_Controller_Plugin_Abstract
{

	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{

		#$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		#$db = $bootstrap->getResource('db');

		#$result = $db->fetchCol(
		#	// select items that are closed
		#	// @todo: add privilege to db table and query
		#   $db->quoteInto('SELECT controller FROM deadlines WHERE abbreviation = ? AND tstart > now() OR tend < now()',
		#   $request->getParam('abbreviation'))
		#);

		#$navigation = $bootstrap->getResource('navigation');
		#foreach ($result as $controller) {
		#	//@todo: need to find one by multiple params, 'controller' and 'action'
		#   $navigation->findOneBy('controller', $controller)->setVisible(false);
		#}

		$acl = Zend_Registry::get('acl');
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
	   		$role = 'guest';
		} else {
			//$role = $auth->getIdentity()->role;
			$role = $auth->getIdentity();
		}

		// whitelist web module
		if ($request->getModuleName() == 'web') {
			return;
		}

		// check if ACL resource exists
		if (!$acl->has(ucfirst($request->getControllerName()))) {
		   return;
		}

		// check if user is allowed to access resource
		if (!$acl->isAllowed($role, ucfirst($request->getControllerName()), $request->getActionName())) {
			// allow all calls to the rest module
			if ($request->getModuleName() == 'rest') {
				return;
			}
			$redir = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');

			$redir->setCode(303)
 				  ->setExit(true)
				  ->gotoRoute(array(
				  	'controller' => 'error',
					'action' => 'noaccess'
				  ), 'main-module');

		}

	}

}
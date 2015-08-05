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
 * @revision   $Id: Acl.php 40 2011-11-29 13:23:56Z gijtenbeek@terena.org $
 */

/**
 * Checks if user is allowed to access resource, if not user gets 303 error
 *
 * @package Application_Plugin
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 * @todo find out why the dispatcher check does not work
 */
class Application_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	$acl = Zend_Registry::get('acl');
    	$auth = Zend_Auth::getInstance();

		// get user role
		$role = ($auth->hasIdentity()) ? $auth->getIdentity() : 'guest';

		// whitelist web module
		if ($request->getModuleName() == 'web') {
			return;
		}

		if ($role != 'guest') {
			// prevent redirect loop by excluding 'user' controller actions
    		if ( ($auth->getIdentity()->email == 'invalid_email_needs_updating')
    			&& ($request->getControllerName() != 'user') ) {

				$flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
				$lastRequest = Zend_Controller_Action_HelperBroker::getStaticHelper('lastRequest');
				$redir = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');

				$flash->addMessage('Your IdP did not provide a valid email address, please supply one below.');
				$lastRequest->saveRequestUri($request->getRequestUri());

				$redir->setCode(303)
					  ->setExit(true)
					  ->gotoRoute(array(
					 	'controller' => 'user',
					 	'action' => 'edit',
					 	'id' => $auth->getIdentity()->user_id
					  ), 'main-module');
			}
		}

    	// check if ACL resource exists
    	if (!$acl->has(ucfirst($request->getControllerName()) )) {
			return;
		}

		// check if user is allowed to access resource
		if (!$acl->isAllowed($role, ucfirst($request->getControllerName()), $request->getActionName())) {
			// allow all calls to the rest module
			if ($request->getModuleName() == 'rest') {
				return;
			}

			$lastRequest = Zend_Controller_Action_HelperBroker::getStaticHelper('lastRequest');
			$redir = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');

			// save last request in session since this data will be lost after redirect
			// have to call it here because the request URI is saved in postDispatch()
			$lastRequest->saveRequestUri($request->getRequestUri());
			// perform redirect
			$redir->setCode(303)
 				  ->setExit(true)
				  ->gotoRoute(array(
				  	'controller' => 'error',
					'action' => 'noaccess',
					'resource' => $acl->get(ucfirst($request->getControllerName()))->getResourceId(),
					'privilege' => $request->getActionName()
				  ), 'main-module');

		}

    }

}
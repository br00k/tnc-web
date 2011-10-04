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
 * @revision   $Id: User.php 623 2011-09-29 13:25:34Z gijtenbeek $
 */

/**
 * User observer. 
 *
 * Following methods can be implemented: _postUpdate, _postInsert, _postDelete
 * @package Core_Model
 * @subpackage Core_Model_Observer
 */
class Core_Model_Observer_User extends TA_Model_Acl_Abstract implements TA_Model_Observer_Interface
{
}
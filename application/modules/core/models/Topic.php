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
 * @revision   $Id: Topic.php 30 2011-10-06 08:37:15Z gijtenbeek@terena.org $
 */

/** 
 * Topic Model
 *
 * @package Core_Model
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class Core_Model_Topic extends TA_Model_Acl_Abstract
{

	/**
	 * Get topic by id
	 * @param		integer		$id		User id
	 * @return		Core_Resource_User_Item
	 */
	public function getTopicById($id)
	{
		$row = $this->getResource('topics')->getTopicById( (int) $id );
		if ($row === null) {
			throw new TA_Model_Exception('id not found');
		}
		return $row;
	}

	public function getTopicsForSelect($type = null)
	{
		return $this->getResource('topics')->getTopicsForSelect($type);
	}

	/**
	 * Get a list of topics
	 * @param		integer		$paged	Page number to show
	 * @param		array		$order	Array with keys 'field' and 'direction'
	 * @return		array		Grid array with keys 'cols', 'primary', 'rows'
	 */
	public function getTopics($paged = null, $order = array())
	{
		if (!$this->checkAcl('list')) {
			throw new TA_Model_Acl_Exception("Insufficient rights");
		}
		return $this->getResource('topics')->getTopics($paged, $order);
	}

	/**
	 * Remove user from resource
	 * @param		integer		$id		Id of record to delete
	 * @return		boolean
	 */
	public function delete($id = null)
	{
		if (!$this->checkAcl('delete')) {
			throw new TA_Model_Acl_Exception("Insufficient rights");
		}

		if (!$id) {
			return false;
		}
		$id = (int) $id;

		return $this->getTopicById($id)->delete();
	}

	/**
	 * Save topic to resource
	 *
	 * @param		array	$post	Post request
	 * @param		string	$action	The subform part to validate against
	 * @return		mixed	The primary key of the inserted/updated record or resource error message
	 * @todo 		This can be abstracted, especially the save files stuff
	 */
	public function saveTopic(array $post, $action = null)
	{
		// perform ACL check
		if (!$this->checkAcl('save')) {
			throw new TA_Model_Acl_Exception("Insufficient rights");
		}

		// get different form based on action parameter
		$formName = ($action) ? 'topic' . ucfirst($action) : 'topic';
		$form = $this->getForm($formName);

		// perform validation
		if (!$form->isValid($post)) {
			return false;
		}

		$values = $form->getValues();

		$topic = array_key_exists('topic_id', $values) ?
			$this->getResource('topics')
				 ->getTopicById($values['topic_id']) : null;

		return $this->getResource('topics')->saveRow($values, $topic);
	}
}
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
 * @revision   $Id: SendEmail.php 598 2011-09-15 20:55:32Z visser $
 */
 
/**
 * Email Helper
 *
 * @author Christian Gijtenbeek
 * @package TA_Controller
 * @subpackage Helper
 */
class TA_Controller_Action_Helper_SendEmail extends Zend_Controller_Action_Helper_Abstract
{
	protected $_subject;

	protected $_template;

	protected $_mail;

	// @todo: replace this with config value!
	protected $_from = 'webmaster@terena.org';

	/**
	 *
	 * @param	array	$config		Array with options:
	 * to:			Recipient
	 * subject:		Subject
	 * template:	Template to use
	 * from:		Sender
	 * html:		boolean		generate HTML email. Template will be suffixed with '.html'
	 * dummy:		integer		If dummmy is 1 then emails will be saved to the filesystem
	 *							They will not be sent. This is useful for reviewing of emails
	 *
	 * @param	array	$values		Array with values to use in the template
	 */
	public function SendEmail(Array $config = array(), Array $values = array())
	{
		if (!isset($config['to_email'])) {
			throw new Exception('Send email: to_email field must be set in config array');
		}

		$this->_mail = new Zend_Mail('UTF-8');

		$toName = (isset($config['to_name'])) ? $config['to_name'] : null;
		$this->setTo($config['to_email'], $toName);

        if (isset($config['subject'])) {
            $this->_subject = $config['subject'];
        }

        if (isset($config['template'])) {
            $this->setTemplate( $config['template'] );
        }

		$fromName = (isset($config['from_name'])) ? $config['from_name'] : null;
        if (isset($config['from'])) {
            $this->setFrom( $config['from'], $fromName);
        }

		$replyToName = (isset($config['reply_to_name'])) ? $config['reply_to_name'] : null;
		if (isset($config['reply_to'])) {
			$this->setReplyTo( $config['reply_to'], $replyToName );
		}

        if (isset($config['bcc'])) {
           $this->_mail->addBcc( $config['bcc'] );
        }

        // this screws up static file downloads
        #$this->_mail->addBcc('webmaster@terena.org');

		$view = new Zend_View();
		// @todo: replace this with config value!
		// link to view helper path
		$view->addHelperPath(APPLICATION_PATH . '/modules/core/views/helpers/', 'Core_View_Helper_');
		// set path to emails
		$view->setScriptPath(APPLICATION_PATH . '/modules/core/views/emails/');

		$view->values = $values;

   		$this->_mail->setSubject($this->_subject);

		/**
		 * If body contains tags, send both HTML and text MIME parts.
		 * Otherwise only send text MIME part.
		 */
		$body = $view->render($this->_template.'.phtml');
		if( strlen($body) != strlen(strip_tags($body))) {
			$this->_mail->setBodyHtml($body)
				    ->setBodyText($this->_textify($body));
		} else {
			$this->_mail->setBodyText($body);
		}



		// if dummy is set, write emails to files instead of actually sending them
        if ( (isset($config['dummy'])) && ($config['dummy'] == 1) ) {
        	$transport = new Zend_Mail_Transport_File(array(
        		'callback' => array($this, 'recipientFilename'),
        		'path' => APPLICATION_PATH . '/../data/mails/'
        	));
        	$this->_mail->send($transport);
        } else {
        	$this->_mail->send();
        }

		return true;
	}

    /**
     * Callback to generate a unique filename. The filename must be generated using
     * values in the config array so it can be generated from other places as well.
     *
     * @return string unique filename
     */
 	public function recipientFilename($transport)
 	{
 		return $transport->recipients . '_'. sha1($this->_subject) .'.eml';
 	}

	/**
	 * Set from field
	 *
	 * @return TA_Controller_Action_Helper_SendEmail fluent interface
	 */
    public function setFrom($from, $fromName = null)
    {
		$this->_mail->setFrom($from, $fromName);
    	return $this;
    }

	/**
	 * Set to field
	 * @return TA_Controller_Action_Helper_SendEmail fluent interface
	 */
    public function setTo($toEmail, $toName = null)
    {
    	$this->_mail->addTo($toEmail, $toName);
    	return $this;
    }

	/**
	 * Set to field
	 * @return TA_Controller_Action_Helper_SendEmail fluent interface
	 */
    public function setReplyTo($replyToEmail, $replyToName = null)
    {
    	$this->_mail->setReplyTo($replyToEmail, $replyToName);
    	return $this;
    }

	/**
	 * Set view template
	 *
	 * @return TA_Controller_Action_Helper_SendEmail fluent interface
	 */
    public function setTemplate($template)
    {
        $this->_template = $template;
        return $this;
    }

	/**
	 * Get view template
	 * @return string
	 */
    public function getTemplate()
    {
    	return $this->_template;
    }

    /**
	 * Proxy method for SendEmail
	 *
	 * @param	array	$config		Configuration
	 * @param	array	$values		Values to use in view template
	 */
    public function direct(array $config = array(), array $values = array())
    {
        return $this->sendEmail($config, $values);
    }

	/**
	 * Flatten HTML e-mail body
	 */
	private function _textify($html)
	{
		$search =	array();
		$replace =	array();

		# New lines & breaks
		$search[]   =   '/([\r\n]){2,}/';
		$replace[]  =   "";

		$search[]  =   '/\n\</';
		$replace[] =   "<";

		$search[]  =   '/([\w.:,])\n(\w)/';
		$replace[] =   "$1 $2";

		$search[]  =   '/\>\n/';
		$replace[] =   ">";

		# <p> => newline
		$search[]   =   '_<p>(.+?)</p>_s';
		$replace[]  =   "$1\n\n";

		# <br />  => newline
		$search[]   =   '_<br\s+/>_';
		$replace[]  =   "\n";

		# <hr />  => newline
		$search[]   =   '_<hr\s+/>_';
		$replace[]  =   "\n";

		# <ul>  => newline
		$search[]   =   '_<ul>(.+?)</ul>_s';
		$replace[]  =   "\n$1";

		# Replace list items by aterisk
		$search[]   =   '_<li>(.+?)</li>_s';
		$replace[]  =   "* $1\n";

		# Shuffle e-mail adres
		$search[]   =   '_<a href=[\'"]mailto:([^"\']*)">([^<]*)</a>_';
		$replace[]  =   "$2 ($1)";

		return strip_tags(html_entity_decode(
			preg_replace($search, $replace, $html), ENT_QUOTES, 'UTF-8'));
	}



}

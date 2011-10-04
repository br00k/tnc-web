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
 * @revision   $Id: Composite.php 598 2011-09-15 20:55:32Z visser $
 */

/**
 * Composite Form Decorator
 *
 * @author Christian Gijtenbeek
 * @package TA_Form
 * @subpackage Decorator
 */
class TA_Form_Decorator_Composite extends Zend_Form_Decorator_Abstract
{

    public function buildLabel()
    {
        $element = $this->getElement();

        $label = $element->getLabel();

        if ($translator = $element->getTranslator()) {
            $label = $translator->translate($label);
        }

        if ($element->isRequired()) {
            $label .= ' *';
        }

		$class = ($element->getType() == 'Zend_Form_Element_Checkbox') ? 'choice' : 'desc';

        return $element->getView()
                       ->formLabel($element->getName(), $label, array('class'=>$class));
    }



	/**
	 *
	 */
    public function buildInput()
    {
        $element = $this->getElement();
        $helper = $element->helper;

		$attributes = $element->getAttribs();
		unset($attributes['helper']); // remove helper attribute

        return $element->getView()->$helper(
            $element->getName(),
            $element->getValue(),
            $attributes,
            $element->options
        );
    }

    public function buildErrors()
    {
        $element = $this->getElement();

        $messages = $element->getMessages();

        if (empty($messages)) {
            return '';
        }

        return $element->getView()->formErrors($messages);
    }


    public function buildDescription()
    {
    	// allow HTML in descriptions
    	$this->setOption('escape', false);
        $element = $this->getElement();

        $desc = $element->getDescription();

        if (empty($desc)) {
            return '';
        }

        return '<div class="description">(' . $desc . ')</div>';
    }


    public function render($content)
    {
        $element = $this->getElement();

        if (!$element instanceof Zend_Form_Element) {
            return $content;
        }

        if (null === $element->getView()) {
            return $content;
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $label     = $this->buildLabel();
        $input     = $this->buildInput();
        $errors    = $this->buildErrors();
        $desc      = $this->buildDescription();

        switch ( $element->getType() ) {
        	case 'Zend_Form_Element_Textarea':
				$output = '<li>'. $label . $errors . $input . $desc . '</li>';
        	break;

        	case 'Zend_Form_Element_Text':
				$output = '<li>'. $label . $errors . $input . $desc . '</li>';
        	break;

        	case 'Zend_Form_Element_Checkbox':
        		$output = '<li>'. $input . $label . $errors . $desc . '</li>';
        	break;

        	case 'Zend_Form_Element_Radio':
        		$output = '<li class="group">'. $label . $input . $errors . $desc . '</li>';
        	break;

        	case 'Zend_Form_Element_File':

        	break;

        	case 'Zend_Form_Element_Hidden':
				$output = '<li class="hidden">'. $input . '</li>';
        	break;

        	case 'TA_Form_Element_Location':
				$element->getView()->headScript()->appendFile('http://maps.google.com/maps/api/js?sensor=false');
				$element->getView()->headScript()->appendFile('/js/jquery-ui/js/jquery-ui.min.js');
				$output = '<li id="location_element">'. $label . $errors . $input . $desc
				. '<input type="text" name="lat" value="" style="display:none;" />'
				. '<input type="text" name="lng" value="" style="display:none;" />'
				. '<div id="map_canvas" class="'.$element->getAttrib('class').'"></div></li>';
        	break;

        	default:
        		$output = '<li>'. $label . $input . $errors . $desc . '</li>';
        	break;
        }

        switch ($placement) {
            case (self::PREPEND):
                return $output . $separator . $content;
            case (self::APPEND):
            default:
                return $content . $separator . $output;
        }

    }

}
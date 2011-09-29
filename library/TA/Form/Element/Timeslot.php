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
 * @revision   $Id: Timeslot.php 598 2011-09-15 20:55:32Z visser $
 */
/**
 * Custom Composite Form Element to represent a CORE timeslot
 *
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class TA_Form_Element_Timeslot extends Zend_Form_Element_Xhtml
{

	protected $_start;
	protected $_end;
	protected $_number;
	protected $_type;

    public function setStart($value)
    {
        $this->_start = $value;
        return $this;
    }

    public function getStart()
    {
        return $this->_start;
    }

    public function setEnd($value)
    {
        $this->_end = $value;
        return $this;
    }

    public function getEnd()
    {
        return $this->_end;
    }

    public function setNumber($value)
    {
        $this->_number = (int) $value;
        return $this;
    }

    public function getNumber()
    {
        return $this->_number;
    }

    public function setType($value)
    {
        $this->_type = (int) $value;
        return $this;
    }

    public function getType()
    {
        return $this->_type;
    }

	/**
	 * Override setName method to allow for use of brackets
	 * in the name property
	 * 
	 * @todo since timeslot[nr] gave problems I no longer need this?
	 */
	public function setName($name)
	{
		$name = $this->filterName($name, true);
        $this->_name = $name;
        return $this;
	}


	/**
	 * Override method to set seperate form values
	 * based on array input
	 *
	 */
	public function setValue($value)
	{
		if (is_array($value)
            && (isset($value['tstart'])
            && isset($value['tend'])
			)
        ) {
            $this->setStart($value['tstart'])
                 ->setEnd($value['tend']);

			if (isset($value['number'])) {
                 $this->setNumber($value['number']);
			}

			if (isset($value['type'])) {
                 $this->setType($value['type']);
			}
        } else {
            throw new Exception('Invalid default timeslot value provided');
        }

	}

	/**
	 * Override method to return nested array values of
	 * seperate elements.
	 *
	 */
	public function getValue()
	{
		return array(
			'tstart' => $this->getStart(),
			'tend' => $this->getEnd(),
			'number' => $this->getNumber(),
			'type' => $this->getType()
		);
	}
	
}
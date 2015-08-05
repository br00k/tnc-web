<?php
/**
 *
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class TA_Form_Decorator_User extends Zend_Form_Decorator_Abstract
{

	/**
	 * view helper
	 */
	private $_view;

	/**
	 * File type is based on db values table: filetypes
	 *
	 */
    public function render($content)
    {

        $element = $this->getElement();

        if (!$element instanceof TA_Form_Element_User) {
            return $content;
        }

		$this->_view = $view = $element->getView();
        if (!$view instanceof Zend_View_Interface) {
            // using view helpers, so do nothing if no view present
            return $content;
        }

        $output = null;

		if ( $linkedUsers = $element->getTaRow()->getUsers() ) {
			$output = '<table>';
			foreach ($linkedUsers as $linkedUser) {
				$output .= '<tr><td>'.$linkedUser['email'].'</td>'
				.'<td>'.$linkedUser['organisation'].'</td>'
				.'<td>'.$this->_getHref($linkedUser['id']).'</td>'
				.'</tr>';
			}

			$output .= '</table>';
		}
		$placement = $this->getPlacement();
		$separator = $this->getSeparator();

		switch ($placement) {
		    case 'PREPEND':
		        return $output . $separator . $content;
		    case 'APPEND':
		    default:
		         return $content . $separator . $output;
		}

    }

	private function _getHref($id)
	{
		return '<a title="remove user" href="'
    	    	.$this->_view->url(array(
		    		'controller' => $this->getElement()->getTaController(),
		    		'action' => 'deleteuserlink',
		    		'id' => $id
		    	), 'main-module') .'">x</a>';	
	
	}

}
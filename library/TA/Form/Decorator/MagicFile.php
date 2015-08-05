<?php
/**
 * Custom File Form Element decorator
 * This element renders an extra list item containing a link to the file
 * Or an inline image if the file is an image
 *
 * @author Christian Gijtenbeek <gijtenbeek@terena.org>
 */
class TA_Form_Decorator_MagicFile extends Zend_Form_Decorator_Abstract
{

	/**
	 * File type is based on db values table: filetypes
	 *
	 */
    public function render($content)
    {

        $element = $this->getElement();

        if (!$element instanceof TA_Form_Element_MagicFile) {
            return $content;
        }

		$view = $element->getView();
        if (!$view instanceof Zend_View_Interface) {
            // using view helpers, so do nothing if no view present
            return $content;
        }

        $output = null;

		if ( $file = $element->getTaFile() ) {
			switch ($type = $file->core_filetype) {
				case 'userimage':
		   			$output = '<li><img src="/core/file/'.$file->file_id.'" alt="userimage_'.$file->file_id.'" /></li>';
		   		break;
		   		case 'submission':
		   			$date = new Zend_Date($file->modified, Zend_Date::ISO_8601);
		   			$output = '<li><a title="download paper" href="/core/file/getfile/id/'.$file->file_id.'">'
		   			.htmlspecialchars($file->filename_orig)
		   			.'</a> ('.$view->timeSince($date->getTimestamp()).' ago'
		   			.')</li>';
		   		break;
		   		case 'paper':
		   			$date = new Zend_Date($file->modified, Zend_Date::ISO_8601);
		   			$output = '<li><a title="download paper" href="/core/file/getfile/id/'.$file->file_id.'">'
		   			.htmlspecialchars($file->filename_orig)
		   			.'</a> ('.$view->timeSince($date->getTimestamp()).' ago'
		   			.')</li>';
		   		break;
		   		case 'slides':
		   			$date = new Zend_Date($file->modified, Zend_Date::ISO_8601);
		   			$output = '<li><a title="download slides" href="/core/file/getfile/id/'.$file->file_id.'">'
		   			.htmlspecialchars($file->filename_orig)
		   			.'</a> ('.$view->timeSince($date->getTimestamp()).' ago'
		   			.')</li>';		   		
		   		break;
		   		case 'location':
		   			$date = new Zend_Date($file->modified, Zend_Date::ISO_8601);
		   			$output = '<li><img src="/core/file/'.$file->file_id.'" alt="location_'.$file->file_id.'" />'
		   			.'</a> ('.$view->timeSince($date->getTimestamp()).' ago'
		   			.')</li>';	   		
		   		break;		   		
		   		case 'misc':
		   			$date = new Zend_Date($file->modified, Zend_Date::ISO_8601);
		   			$output = '<li><a title="download slides" href="/core/file/getfile/id/'.$file->file_id.'">'
		   			.htmlspecialchars($file->filename_orig)
		   			.'</a> ('.$view->timeSince($date->getTimestamp()).' ago'
		   			.')</li>';				   		
		   		break;
		   		default:
		   			$date = new Zend_Date($file->modified, Zend_Date::ISO_8601);
		   			$output = '<li><a title="download file" href="/core/file/getfile/id/'.$file->file_id.'">'
		   			.htmlspecialchars($file->filename_orig)
		   			.'</a> ('.$view->timeSince($date->getTimestamp()).' ago'
		   			.')</li>';
		   		break;
			}

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
}
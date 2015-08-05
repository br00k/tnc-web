<?php
/**
 * Make sure users can only change their own information
 *
 */
class Core_Model_Acl_DateAssertion implements Zend_Acl_Assert_Interface
{
    /**
     * This assertion should receive the actual User objects.
     *
     * @param Zend_Acl $acl
     * @param Zend_Acl_Role_Interface $user
     * @param Zend_Acl_Resource_Interface $model
     * @param $privilege
     * @return bool
     */
    public function assert(Zend_Acl $acl, Zend_Acl_Role_Interface $user = null, Zend_Acl_Resource_Interface $model = null, $privilege = null)
    {
		//Zend_Debug::dump('asserting ' . $model->getResourceId() . ' '. $privilege);

		#foreach ($result as $controller) {
		#	if (ucfirst($controller) === $model->getResourceId()) {
		#		return false;
		#	}
		#}
   

    
    foreach ($trace = debug_backtrace() as $level) {
    	//$file   = $level['file'];
    	//$line   = $level['line'];
    	//$class	= $level['class'];    
    	#Zend_Debug::dump("called: line $line of $class \n(in $file)");
    }
    		
		
		$log = Zend_Registry::get('log');
		$log->info(__CLASS__);
				
		#Zend_Debug::dump(Zend_Registry::get('conference'));exit();
		
		return false;
    }
}

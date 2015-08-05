<?php

class Core_Model_Acl_Role_Reviewer implements Zend_Acl_Role_Interface
{
    public function getRoleId()
    {
        return 'reviewer';
    }
}

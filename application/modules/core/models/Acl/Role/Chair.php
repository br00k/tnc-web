<?php

class Core_Model_Acl_Role_Chair implements Zend_Acl_Role_Interface
{
    public function getRoleId()
    {
        return 'chair';
    }
}

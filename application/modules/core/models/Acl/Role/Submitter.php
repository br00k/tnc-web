<?php

class Core_Model_Acl_Role_Submitter implements Zend_Acl_Role_Interface
{
    public function getRoleId()
    {
        return 'submitter';
    }
}

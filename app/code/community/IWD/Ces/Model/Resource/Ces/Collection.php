<?php

class IWD_Ces_Model_Resource_Ces_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('iwd_ces/ces');
    }

}
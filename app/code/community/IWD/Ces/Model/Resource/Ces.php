<?php

class IWD_Ces_Model_Resource_Ces extends Mage_Core_Model_Mysql4_Abstract
{

    public function _construct()
    {
        $this->_init('iwd_ces/table_iwd_ces', 'id');
    }

}
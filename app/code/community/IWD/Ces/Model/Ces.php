<?php

class IWD_Ces_Model_Ces extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('iwd_ces/ces');
    }
}
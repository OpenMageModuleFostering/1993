<?php

class IWD_Ces_Frontend_IndexController extends Mage_Core_Controller_Front_Action
{
    public function viewAction()
    {
        $this->loadLayout(['default', 'iwd_ces_index_view']);
        // get breadcrumbs block
        if (!Mage::getStoreConfig(IWD_Ces_Helper_Settings::XML_PATH_SHOW_BREADCRUMBS)) {
            $this->getLayout()->getBlock('root')->unsetChild('breadcrumbs');
        }
        $this->renderLayout();
    }
}
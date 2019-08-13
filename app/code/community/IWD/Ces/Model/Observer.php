<?php
class IWD_Ces_Model_Observer {

    public function disableCache(Varien_Event_Observer $observer)
    {
        $action = $observer->getEvent()->getControllerAction();

        //if ($action instanceof IWD_Ces_Frontend_IndexController) { // eg. Mage_Catalog_ProductController
        $request = $action->getRequest();
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('full_page');
        $cache->banUse(Mage_Core_Block_Abstract::CACHE_GROUP);
        //}
    }
}
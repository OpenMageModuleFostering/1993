<?php

class IWD_Ces_Block_Frontend_View_Success extends Mage_Checkout_Block_Onepage_Success
{
    public function __construct() {
        parent::__construct();
    }

    public function getOrderAmount() {
        $order = $this->getOrder();
        if ($order != null) {
            return $order->getSubtotal();
        }
    }

    public function getOrderCurrency() {
        $order = $this->getOrder();
        if ($order != null) {
            return $order->getOrderCurrency()->getCode();
        }
    }

    public function getOrder() {
        $last_order_id = Mage::getSingleton('checkout/session')->getLastOrderId();
        return Mage::getModel('sales/order')->load($last_order_id);
    }

    public function getUsersEmail() {
        if ($order = $this->getOrder()) {
            return $order->getCustomerEmail();
        }
    }

    public function getUsersName() {
        if ($order = $this->getOrder()) {
            return $order->getCustomerName();
        }
    }

    public function getStoreName() {
        return Mage::app()->getStore()->getFrontendName();
    }

    /**
     * @return null|string
     * get all products from current order , sort them and return sku
     * of product with biggest price
     */
    public function getProductSku() {
        if ($order = $this->getOrder()) {
            if ($orderedItems = $order->getAllVisibleItems()) {
                if (is_array($orderedItems) && count($orderedItems) > 0) {
                    $price_array = [];
                    foreach ($orderedItems as $item) {
                        $price_array[$item->getProductId()] = $item->getPrice();
                    }
                    arsort($price_array);
                    $id = key($price_array);
                    //$id = $price_array[0]->getProductId(); // getting first product sku
                    if ($product = Mage::getModel('catalog/product')->load($id)) {
                        return $product->getSku();
                    }
                }
            }
        }
        return null;
    }
}
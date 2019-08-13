<?php

class IWD_Ces_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard {

    const MODULE = 'IWD_Ces_Frontend';
    const INDEX_CONTROLLER = 'index';

    const INDEX_ACTION = 'index';
    const VIEW_ACTION = 'view';

    public function match(Zend_Controller_Request_Http $request)
    {
        if(!Mage::getStoreConfig(IWD_Ces_Helper_Settings::XML_PATH_GENERAL_ENABLED)) {
            return false;
        }
        $path = explode('/', trim($request->getPathInfo(), '/'));

        $r_path = Mage::getModel('core/url_rewrite')
            ->setStoreId(Mage::app()->getStore()->getStoreId())
            ->loadByRequestPath($path[0] . '.html'); // not good code, but who care?

        $model = Mage::getModel('catalog/product');
        if ($arr = $r_path->getIdPath()) {
            $r = explode('/', $arr);
            $id = isset($r[1]) ? $r[1] : null; // id is second
        }
        $product = isset($id) ? $model->load($id) : $model->loadByAttribute('url_key', $path[0]);

        // If path doesn't match your module requirements
        if (count($path) != 2) {
            return false;
        }
        // if product isn't exist
        if (!$product) {
            return false;
        }
        // if isset filter - set filter, if not - set default
        $questions_filter = isset($path[1]) ? explode('&', $path[1])[0] : 'questions';

        $realModule = self::MODULE;
        $controller = self::INDEX_CONTROLLER;
        $action = self::VIEW_ACTION;
        $controllerClassName = $this->_validateControllerClassName(
            $realModule,
            $controller
        );
        // If controller was not found
        if (!$controllerClassName) {
            return false;
        }
        // Instantiate controller class
        $controllerInstance = Mage::getControllerInstance(
            $controllerClassName,
            $request,
            $this->getFront()->getResponse()
        );
        // If action is not found
        if (!$controllerInstance->hasAction($action)) {
            return false;
        }
        // Set request data
        $request->setModuleName($realModule);
        $request->setControllerName($controller);
        $request->setActionName($action);
        $request->setControllerModule($realModule);
        // Set your custom request parameter
        $request->setParam('id', $product->getId());
        $request->setParam('filter', $questions_filter);
        $request->setParam('url_key', $path[0]);
        $page_path = explode('&', $path[1]);
        if (isset($page_path[1])) {
            $get_param = explode('=', $page_path[1]);
            $request->setParam($get_param[0], $get_param[1]);
        }
        // dispatch action
        $request->setDispatched(true);
        $controllerInstance->dispatch($action);
        // Indicate that our route was dispatched
        return true;
    }
}
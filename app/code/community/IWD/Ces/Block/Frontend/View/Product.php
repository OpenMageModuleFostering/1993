<?php
class IWD_Ces_Block_Frontend_View_Product extends Mage_Catalog_Block_Product_View {

    protected $_type;

    protected function sendCurl($url) {
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($curl);
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
        }
        return $code != 200 ? null : json_decode($data, true);
    }

    public function getFeatureRequestStatusesArray() {
        return [
            'IN_REVIEW' => 1,
            'COMING_SOON' => 2,
            'COMPLETED' => 3,
        ];
    }

    public function getDomain() {
        return Mage::helper('iwd_ces/settings')->getDomain();
    }

    function _construct() {
        if (!Mage::registry('current_product') || !Mage::registry('product')) {
            if ($product = $this->getProduct()) {
                Mage::register('current_product', $product);
                Mage::register('product', $product);
                $this->setProduct($product);
            }
        }
        parent::_construct();
    }

    public function getProduct() {
        if ($product_id = Mage::app()->getRequest()->getParam('id')) {
            $product = Mage::getModel('catalog/product')->load($product_id);
            if ($product) { return $product; }
        }
        return null;
    }

    public function getWidgetId($type = null) {
        $type = $type ? $type : $this->getWidgetType();
        switch($type) {
            case 'questions':
                if (Mage::getStoreConfig(IWD_Ces_Helper_Settings::XML_PATH_SHOW_QUESTIONS_ANSWERS)) {
                    return Mage::getStoreConfig(IWD_Ces_Helper_Settings::XML_PATH_QA_WIDGET_ID);
                }
                break;
            case 'requests':
                if (Mage::getStoreConfig(IWD_Ces_Helper_Settings::XML_PATH_SHOW_FEATURE_REQUEST)) {
                    return Mage::getStoreConfig(IWD_Ces_Helper_Settings::XML_PATH_FR_WIDGET_ID);
                }
                break;
            case 'reviews':
                if (Mage::getStoreConfig(IWD_Ces_Helper_Settings::XML_PATH_SHOW_REVIEWS)) {
                    return Mage::getStoreConfig(IWD_Ces_Helper_Settings::XML_PATH_REVIEWS_WIDGET_ID);
                }
                break;
        }
        return null;
    }

    public function getCount($type, $status = null) {
        if ($product  = $this->getProduct()) {
            $sku = $product->getSku();
            $widget = $this->getWidgetId($type);
            $domain = $this->getDomain();
            $url = "https://$domain/product-suite/api/products/$sku/widget/$widget/$type-count";
            if ($status) {
                $url .= '?status=' . (string)$status;
            }
            return $this->sendCurl($url);
        }
        return null;
    }

    public function getReviewsRating() {
        if ($product  = $this->getProduct()) {
            $sku = $product->getSku();
            $widget = $this->getWidgetId('reviews');
            $domain = $this->getDomain();
            $url = "https://$domain/product-suite/api/products/$sku/widget/$widget/reviews-rating";

            return $this->sendCurl($url);
        }
        return null;
    }

    public function getProductUrlStr() {
        // get request string
        if (Mage::app()->getRequest()->getParam('category')) {
            $p = $this->getProduct();
            $id_path = 'product/' . $p->getId();
            $r_path = Mage::getModel('core/url_rewrite')
                ->setStoreId(Mage::app()->getStore()->getStoreId())
                ->loadByIdPath($id_path);
            $str = $r_path->getRequestPath();
        } else $str = Mage::app()->getRequest()->getRequestString();
        $path = explode('/', $str);
        if (count($path) > 0) {
            // get original str without .html
            $res = explode('.', $path[count($path) - 1]);
            if (count($res) > 0) {
                return $res[0];
            }
        }
    }

    public function getFilter() {
        if ($filter = Mage::app()->getRequest()->getParam('filter')) {
            if ($filter == 'feature-requests') {
                return 'requests';
            }
            return $filter;
        }
        return 'questions';
    }

    public function getUrlKey() {
        if ($key = Mage::app()->getRequest()->getParam('url_key')) {
            return $key;
        }
        return null;
    }

    public function getPage() {
        return  Mage::app()->getRequest()->getParam('page');
    }

    public function getPerPage() {
        return Mage::app()->getRequest()->getParam('per-page');
    }

    public function getWidgetType() {
        if (!$this->_type) {
            $_filter = $this->getFilter();
            $this->_type = $_filter == 'questions'
                || $_filter == 'requests'
                || $_filter == 'reviews' ?
                $_filter :
                $this->requestForType($_filter)['type'];
            // transform to one type
            if ($this->_type == 'featurerequest') {
                $this->_type = 'requests';
            } else if ($this->_type == 'review') {
                $this->_type = 'reviews';
            }
        }
        return $this->_type;
    }

    public function requestForType($id) {
        if ($product  = $this->getProduct()) {
            $sku = $product->getSku();
            $domain = $this->getDomain();
            return $this->sendCurl("https://$domain/product-suite/api/products/$sku/get-type/$id");
        }

        return null;
    }

    public function getFeatureRequestStatus() {
        $status_array = $this->getFeatureRequestStatusesArray();
        if ($this->getWidgetType() == 'requests') {
            if ($status = Mage::app()->getRequest()->get('status')) {
                return array_key_exists((string)$status, $status_array) ? (string)$status_array[(string)$status] : 1;
            }
        }
        return null;
    }

    public function getSorting() {
        return Mage::app()->getRequest()->getParam('sort');
    }

    public function getWidgetBody($data = []) {
        if (!empty($data)) {
            $product = $this->getProduct();
            // default product option with needed
            $product_options = [
                'product_id' => $product->getSku(),
                'product_name' => $product->getName(),
                'product_alias' => $product->getSku(),
            ];

            if ($sort = $this->getSorting()) {
                $product_options['sort'] = $sort;
            }

            $data = array_merge($data, $product_options);
            $url = 'https://' . $this->getDomain() . "/product-suite/frontend-widget/batch";
            $options = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
                'http' => [
                    'protocol_version' => 1.1,
                    'header'           => [
                        'Content-type: application/x-www-form-urlencoded',
                        'Connection: close',
                    ],
                    'method'  => 'POST',
                    'request_fulluri' => true,
                    'content' => http_build_query($data),
                ]
            ];
            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            // Parsing the response
            $response = @json_decode($result, true);
            return $response;
        }
    }

    public function mathRating($rating, $round = false, $stars_count = 5) {
        $ratings = [];
        $rating = round($rating, 2);
        for($i = 1; $i <= $stars_count; $i++) {
            if(!$round) {
                if($rating > (($i - 1) + 0.33) && $rating < (($i - 1) + 0.67))
                    $ratings[$i] = '1/2';
                else if($rating < $i - 0.33) {
                    $ratings[$i] = '0';
                }
                else
                    $ratings[$i] = '1';
            }
            else {
                if($rating < $i) {
                    $ratings[$i] = '0';
                }
                else
                    $ratings[$i] = '1';
            }
        }
        return $ratings;
    }
}
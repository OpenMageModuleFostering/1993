<?php
class IWD_Ces_Helper_Settings extends Mage_Core_Helper_Abstract
{
    const XML_PATH_GENERAL_ENABLED        = "iwd_ces/general_extension/enabled";
    const XML_PATH_QA_WIDGET_ID           = "iwd_ces/questions_answers/qa_widget_id";
    const XML_PATH_FR_WIDGET_ID           = "iwd_ces/feature_requests/feature_request_widget_id";
    const XML_PATH_REVIEWS_WIDGET_ID      = "iwd_ces/reviews/reviews_widget_id";
    const XML_PATH_SHOW_BREADCRUMBS       = "iwd_ces/other/show_breadcrumbs";
    const XML_PATH_SHOW_QUESTIONS_ANSWERS = "iwd_ces/questions_answers/show_questions_answers";
    const XML_PATH_SHOW_FEATURE_REQUEST   = "iwd_ces/feature_requests/show_feature_request";
    const XML_PATH_SHOW_REVIEWS           = "iwd_ces/reviews/show_reviews";

    public function isEnabled() {
        return Mage::getStoreConfig(self::XML_PATH_GENERAL_ENABLED);
    }

    public function getQAWidgetId() {
        return Mage::getStoreConfig(self::XML_PATH_QA_WIDGET_ID);
    }

    public function getFRWidgetId() {
        return Mage::getStoreConfig(self::XML_PATH_FR_WIDGET_ID);
    }

    public function getReviewsWidgetId() {
        return Mage::getStoreConfig(self::XML_PATH_REVIEWS_WIDGET_ID);
    }

    public function showBreadCrumbs() {
        return Mage::getStoreConfig(self::XML_PATH_SHOW_BREADCRUMBS);
    }

    public function showQAWidget() {
        return Mage::getStoreConfig(self::XML_PATH_SHOW_QUESTIONS_ANSWERS);
    }

    public function showFRWidget() {
        return Mage::getStoreConfig(self::XML_PATH_SHOW_FEATURE_REQUEST);
    }

    public function showReviewsWidget() {
        return Mage::getStoreConfig(self::XML_PATH_SHOW_REVIEWS);
    }

    public function getDomain() {
        // const
        $base_url = 'www.iwdagency.com';
        // block for local development
        $crnt_url = $_SERVER['HTTP_HOST'];
        if ($crnt_url == 'dev.weeetail.com'
            || $crnt_url == 'staging.weeetail.com') {
            $base_url = $crnt_url;
        }
        if ($crnt_url == 'local-dev-weeetail.com') {
            $base_url = 'dev.weeetail.com';
        }
        return $base_url;
    }
}
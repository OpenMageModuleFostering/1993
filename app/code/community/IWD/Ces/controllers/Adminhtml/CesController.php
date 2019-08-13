<?php

class IWD_Ces_Adminhtml_CesController extends Mage_Adminhtml_Controller_Action
{
    public function importAction()
    {
        $session = $this->_getSession();

        $model = Mage::getModel('iwd_ces/ces');

        if (count($model->getCollection()) > 0) {
            $session->addNotice('Can\'t import, already done');
            return $this->_redirect('adminhtml/system_config/edit/section/iwd_ces');
        }

        $activeReviews = Mage::getModel('review/review')
            ->getResourceCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
            ->setDateOrder()
            ->addRateVotes();

        $pendingReviews = Mage::getModel('review/review')
            ->getResourceCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addStatusFilter(Mage_Review_Model_Review::STATUS_PENDING)
            ->setDateOrder()
            ->addRateVotes();

        $notApprovedReviews = Mage::getModel('review/review')
            ->getResourceCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addStatusFilter(Mage_Review_Model_Review::STATUS_NOT_APPROVED)
            ->setDateOrder()
            ->addRateVotes();
        try {
            $this->sendData($activeReviews, Mage_Review_Model_Review::STATUS_APPROVED);
            $this->sendData($pendingReviews, Mage_Review_Model_Review::STATUS_PENDING);
            $this->sendData($notApprovedReviews, Mage_Review_Model_Review::STATUS_NOT_APPROVED);;
            $model->setImported(1);
            $model->save();
            $session->addSuccess('Your reviews were successfully imported');
        } catch (Exception $e) {
            $session->addError($e->getMessage());
        }
        return $this->_redirect('adminhtml/system_config/edit/section/iwd_ces');
    }


    private function sendData(&$reviews, $status) {
        $domain = Mage::helper('iwd_ces/settings')->getDomain();
        $url = "https://$domain/product-suite/api/review/import";
        $widgetId = Mage::helper('iwd_ces/settings')->getReviewsWidgetId();

        foreach ($reviews as $review) {
            $product = Mage::getModel('catalog/product')->load($review->getEntityPkValue());

            $counter = 0;
            $cumulative = 0;
            foreach($review->getRatingVotes() as $vote) {
                $cumulative += $vote->getPercent();
                $counter++;
            }
            $finalPercentage = 0;
            if ($cumulative != 0) {
                $finalPercentage = ($cumulative / $counter);
            }

            $st = null;
            switch ($status) {
                case Mage_Review_Model_Review::STATUS_APPROVED:
                    $st = 'approved';
                    break;
                case Mage_Review_Model_Review::STATUS_NOT_APPROVED:
                    $st = 'not-approved';
                    break;
                case Mage_Review_Model_Review::STATUS_PENDING:
                    $st = 'pending';
                    break;
                default:
                    $st = 'undefined';
                    break;
            }

            $data = [
                'widget_id' => $widgetId,
                'product_sku' => $product->getSku(),
                'product_id' => $product->getId(),
                'product_url' => $product->getProductUrl(),
                'product_name' => $product->getName(),
                'title' => $review->getTitle(),
                'detail' => $review->getDetail(),
                'name' => $review->getNickname(),
                'percent' => $finalPercentage,
                'status' => $st,
            ];

            $customerId = $review->getCustomerId();
            if ($customerId) {
                $customerData = Mage::getModel('customer/customer')->load($customerId);
                $data['email'] = $customerData->getEmail();
            }

            $result = Mage::helper('iwd_ces/curl')->post($url, $data);
            if (!$result) {
                $errorText = Mage::helper('iwd_ces')
                    ->__('You have some error. Try again later please');
                throw new Exception($errorText);
            }
        }

    }
}
<?php
require_once Mage::getModuleDir('controllers', 'Mage_CatalogSearch') . DS . 'AdvancedController.php';

class Morozov_Similarity_AdvancedController
extends Mage_CatalogSearch_AdvancedController
{
    public function resultAction()
    {
        $this->loadLayout();
        try {
            try {
                Mage::getSingleton('catalogsearch/advanced')->addFilters($this->getRequest()->getQuery());
                if ($similar = $this->getRequestHelper()->getSimilar()) {
                    $this->addSimilarFilters(Mage::getSingleton('catalogsearch/advanced'), $similar);
                }
            } catch (Mage_Core_Exception $e) {
                if (($similar = $this->getRequestHelper()->getSimilar()) && $this->detectTermsNotSpecifiedMsg($e->getMessage())) {
                    $this->addSimilarFilters(Mage::getSingleton('catalogsearch/advanced'), $similar);
                } else {
                    throw $e;
                }
            }
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('catalogsearch/session')->addError($e->getMessage());
            $this->_redirectError(
                Mage::getModel('core/url')
                    ->setQueryParams($this->getRequest()->getQuery())
                    ->getUrl('*/*/')
            );
        }
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    protected function detectTermsNotSpecifiedMsg($message)
    {
        $res = stristr($message, Mage::helper('catalogsearch')->__('Please specify at least one search term.'));
        return $res;
    }

    protected function addSimilarFilters(Mage_CatalogSearch_Model_Advanced $advanced, $similar)
    {
        $ids = [];
        try {
            $ids = @$this->getApiHelper()->getUpSells((int)$similar);
            $advanced->getProductCollection()
                ->addFieldToFilter('entity_id', ['in' => $ids])
            ;
        } catch (Exception $e) {
            $this->getDefaultHelper()->log('Advanced Search: ' . $e->getMessage());
        }
        if (!$ids) {
            throw new Mage_Core_Exception("Couldn't get similar products from the service..");
        }

    }

    protected function getRequestHelper()
    {
        return Mage::helper('morozov_similarity/request');
    }

    protected function getApiHelper()
    {
        return Mage::helper('morozov_similarity/api');
    }

    protected function getDefaultHelper()
    {
        return Mage::helper('morozov_similarity');
    }
}

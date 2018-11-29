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
                $this->addSimilarFiltersIfNeed(Mage::getSingleton('catalogsearch/advanced'));
            } catch (Mage_Core_Exception $e) {
                if ($this->detectTermsNotSpecifiedMsg($e->getMessage())) {
                    $this->addSimilarFiltersIfNeed(Mage::getSingleton('catalogsearch/advanced'));
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

    protected function getSimilar()
    {
        $similar = $this->getRequest()->getParam('similar');
        return $similar;
    }

    protected function addSimilarFiltersIfNeed(Mage_CatalogSearch_Model_Advanced $advanced)
    {
        // @TODO: get all similar Products from the service
        if ($similar = $this->getSimilar()) {
            /*
            $advanced->getProductCollection()
                ->addFieldToFilter('entity_id', ['in' => [340, 31660]])
            ;
            */
            //throw new Mage_Core_Exception('2222');
        }
    }
}

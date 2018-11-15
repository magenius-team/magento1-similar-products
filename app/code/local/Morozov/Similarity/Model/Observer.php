<?php

class Morozov_Similarity_Model_Observer
{
    public function onCatalogProductUpsell($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        if ($this->getDefaultHelper()->canUse()
            && ($collection instanceof Morozov_Similarity_Model_Resource_UpSellProductCollection)) {
            /** Begin Compatibility with Enterprise Edition */
            $orderPart = $collection->getSelect()->getPart(Zend_Db_Select::ORDER);
            $orderPart = array_filter($orderPart, function ($f) {
                return !is_array($f);
            });
            $collection->getSelect()->setPart(Zend_Db_Select::ORDER, $orderPart);
            /** End Compatibility with Enterprise Edition */
            $collection
                ->setIsNotLoaded()
                ->setPageSize($this->getDefaultHelper()->getUpSellMaxCount())
                ->load();
        }
    }

    public function setProducts($observer)
    {
        foreach($this->getDefaultHelper()->getStores() as $store) {
            try {
                $this->getDefaultHelper()->setStoreId($store->getStoreId());
                if ($this->getDefaultHelper()->getCronEnabled()) {
                    $this->getDefaultHelper()->log('');
                    $this->getDefaultHelper()->log("Pushing Products to the service (Store ID = {$store->getStoreId()}): ");
                    $this->getApiHelper()->setAllProducts();
                    $this->getDefaultHelper()->log('Done.');
                }
            } catch (Exception $e) {
                $this->getDefaultHelper()->log($e->getMessage());
            }
        }
    }

    protected function getDefaultHelper()
    {
        return Mage::helper('morozov_similarity');
    }

    protected function getApiHelper()
    {
        return Mage::helper('morozov_similarity/api');
    }
}

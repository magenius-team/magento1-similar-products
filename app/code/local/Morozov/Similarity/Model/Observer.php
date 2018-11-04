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
        //if ($this->getDefaultHelper()->canUse()) {
            try {
                Mage::helper('morozov_similarity/api')->setAllProducts();
            } catch (Exception $e) {
                $this->getDefaultHelper()->log($e->getMessage());
            }
        //}
    }

    protected function getDefaultHelper()
    {
        return Mage::helper('morozov_similarity');
    }
}

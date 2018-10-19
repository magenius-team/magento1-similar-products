<?php
class Morozov_Similarity_Model_Observer
{
    public function onCatalogProductUpsell($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        if ($this->getDefaultHelper()->canUse()
          && ($collection instanceof Morozov_Similarity_Model_Resource_UpSellProductCollection)) {
            $collection
                ->setIsNotLoaded()
                ->setPageSize($this->getDefaultHelper()->getUpSellMaxCount())
                ->load()
            ;
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

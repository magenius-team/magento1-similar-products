<?php
class Morozov_Similarity_Model_Observer
{
    public function onCatalogProductUpsell($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        //Mage::log(get_class($collection)); // Mage_Catalog_Model_Resource_Product_Link_Product_Collection

        if ($this->getDefaultHelper()->canUse()) {
            $collection
                ->setIsNotLoaded()
                ->setPageSize($this->getDefaultHelper()->getUpSellMaxCount())
                ->load()
            ;
        }
    }

    public function setProducts($observer)
    {
        try {
            Mage::helper('morozov_similarity/api')->setAllProducts();
        } catch (Exception $e) {
            $this->getDefaultHelper()->log($e->getMessage());
        }
    }

    protected function getDefaultHelper()
    {
        return Mage::helper('morozov_similarity');
    }
}

<?php
class Morozov_Similarity_Model_Observer
{
    public function onCatalogProductUpsell($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        //Mage::log(get_class($collection));

        if (Mage::helper('morozov_similarity')->canUse()) {
            $collection
                ->setIsNotLoaded()
                ->setPageSize(Mage::helper('morozov_similarity')->getUpSellMaxCount())
                ->load()
            ;
        }
    }

    public function setProducts($observer)
    {
        Mage::helper('morozov_similarity/api')->setAllProducts();
    }
}

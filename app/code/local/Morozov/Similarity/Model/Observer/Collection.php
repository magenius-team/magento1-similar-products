<?php
class Morozov_Similarity_Model_Observer_Collection
{
    protected static $isFiltered = false;

    public function onCatalogProductCollectionLoadBefore($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        //Mage::log(get_class($collection));
        //Mage::log($collection->getSelect()->assemble());
        //
    }

    protected function detectCatalogProductCollection($collection)
    {
        $res = $collection instanceof Mage_Catalog_Model_Resource_Product_Collection;
        return $res;
    }
}

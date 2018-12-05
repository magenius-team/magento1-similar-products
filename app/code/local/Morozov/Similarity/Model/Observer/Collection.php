<?php
class Morozov_Similarity_Model_Observer_Collection
{
    protected static $isFiltered = false;

    public function onCatalogProductCollectionLoadBefore($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        /*
        if ($this->detectCatalogProductCollection($collection) && (!self::$isFiltered)) {
            // @TODO: get similar products from the service
            $collection
                ->addFieldToFilter('entity_id', ['in' => [427, 233, 2064, 31075, 31604, 331, 227, 31554, 332, 31523, 230, 333, 231, 228, 407]])
            ;
            //Mage::log($collection->getSelect()->assemble());
            self::$isFiltered = true;
            //Mage::getSingleton('core/session')->addError('Eeeeeee44');
        }
        */
    }

    protected function detectCatalogProductCollection($collection)
    {
        $res = $collection instanceof Mage_Catalog_Model_Resource_Product_Collection;
        return $res;
    }
}

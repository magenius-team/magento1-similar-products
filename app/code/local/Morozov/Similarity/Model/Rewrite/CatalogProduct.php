<?php
class Morozov_Similarity_Model_Rewrite_CatalogProduct extends Morozov_Similarity_Model_Product
{
    // override parent
    public function getUpSellProductCollection()
    {
        if (Mage::helper('morozov_similarity')->canUse()) {

            $ids = Mage::helper('morozov_similarity/api')->getUpSells($this->getEntityId());
            //Mage::log($ids);
            if (Mage::helper('morozov_similarity')->isDummy()) {
                $ids = [32732, 32733, 32734, 32735]; // dummy
            }

            $collection = Mage::getResourceModel('morozov_similarity/upSellProductCollection')
                ->addFieldToFilter('entity_id', ['in' => $ids])
            ;
            return $collection;
        }

        return parent::getUpSellProductCollection();
    }
}

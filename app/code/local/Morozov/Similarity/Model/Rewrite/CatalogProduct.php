<?php
class Morozov_Similarity_Model_Rewrite_CatalogProduct extends Morozov_Similarity_Model_Product
{
    // override parent
    public function getUpSellProductCollection()
    {
        if (Mage::helper('morozov_similarity')->canUse()) {
            try {
                $ids = Mage::helper('morozov_similarity/api')->getUpSells($this->getEntityId());
                $collection = Mage::getResourceModel('morozov_similarity/upSellProductCollection')
                    ->addFieldToFilter('entity_id', ['in' => $ids]);
                return $collection;
            } catch (Exception $e) {
                Mage::helper('morozov_similarity')->log($e->getMessage());
            }
        }

        return parent::getUpSellProductCollection();
    }
}

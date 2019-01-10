<?php

class Morozov_Similarity_Block_Rewrite_CatalogProductListUpsell
    extends Mage_Catalog_Block_Product_List_Upsell
{
    protected function _prepareData()
    {
        $product = Mage::registry('product');

        /* @var $product Mage_Catalog_Model_Product */
        $this->_itemCollection = $this->getUpsellProductCollection($product)
            ->setPositionOrder()
            ->addStoreFilter();

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
            Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_itemCollection,
                Mage::getSingleton('checkout/session')->getQuoteId()
            );

            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
//        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($this->_itemCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);

        if ($this->getItemLimit('upsell') > 0) {
            $this->_itemCollection->setPageSize($this->getItemLimit('upsell'));
        }

        if ($this->getDefaultHelper()->canUse()) {
            $this->adjustItemCollection($this->_itemCollection);
        }

        $this->_itemCollection->load();

        /**
         * Updating collection with desired items
         */
        Mage::dispatchEvent('catalog_product_upsell', array(
            'product' => $product,
            'collection' => $this->_itemCollection,
            'limit' => $this->getItemLimit()
        ));

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    protected function getUpsellProductCollection($product)
    {
        if ($this->getDefaultHelper()->canUse()) {
            try {
                if ($ids = $this->getApiHelper()->getUpSells($product->getEntityId())) {
                    $collection = Mage::getResourceModel('morozov_similarity/upSellProductCollection')
                        ->addFieldToFilter('entity_id', ['in' => $ids]);
                    $orderIds = implode(',', $ids);
                    $collection->getSelect()->order(new Zend_Db_Expr("FIELD(e.entity_id, $orderIds)"));
                    return $collection;
                }
            } catch (Exception $e) {
                $this->getDefaultHelper()->log($e->getMessage());
            }
        }
        return $product->getUpSellProductCollection();
    }

    protected function adjustItemCollection($collection)
    {
        // Begin Compatibility with Enterprise Edition
        $orderPart = $collection->getSelect()->getPart(Zend_Db_Select::ORDER);
        $orderPart = array_filter($orderPart, function ($f) {
            return !is_array($f);
        });
        $collection->getSelect()->setPart(Zend_Db_Select::ORDER, $orderPart);
        // End Compatibility with Enterprise Edition
        $collection->setPageSize($this->getDefaultHelper()->getUpSellMaxCount());
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

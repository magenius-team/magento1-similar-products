<?php
class Morozov_Similarity_Model_Observer_Collection
{
    //protected static $isFiltered = false;

    protected static $lockLoadAfter = false;

    public function onCatalogProductCollectionLoadBefore($observer)
    {
        $collection = $observer->getEvent()->getCollection();
    }

    public function onCatalogProductCollectionLoadAfter($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        if ($this->detectCatalogProductCollection($collection)) {
            if ($similar = $this->getRequestHelper()->getSimilar()) {
                if (!$this->getDefaultHelper()->getCustomSortByForCategoryEnabled()) {
                    return;
                }

                try {
                    if ($ids = @$this->getApiHelper()->getUpSells((int)$similar)) {
                    //if ($ids = [30934, 31437, 31487]) {
                        if (self::$lockLoadAfter) {
                            self::$lockLoadAfter = false;
                            return;
                        }

                        self::$lockLoadAfter = true;
                        $collection->clear();
                        $collection->addFieldToFilter('entity_id', array('in' => $ids));

                        $this->setCollectionSort($collection, $ids);
                        return;
                    }
                } catch (Exception $e) {
                    $this->getDefaultHelper()->log('Product List: ' . $e->getMessage());
                }

                $collection->clear();
                $collection->addFieldToFilter('entity_id', null);
            }
        }
    }

    protected function setCollectionSort($collection, $ids)
    {
        // Should be at the very top level, before Blocks rendering
        if (!Mage::getSingleton('catalog/session')->getData('sort_order')) {
            Mage::getSingleton('catalog/session')
                ->setData('sort_order', $this->getRequestHelper()->getSimilarVarName())
                //->setData('sort_direction', 'desc')
            ;
        }

        if ($this->detectSimilarSort()) {
            $collection->getSelect()->setPart(Zend_Db_Select::ORDER, array());
            $ids = ($this->getSortDirection() == 'desc') ? array_reverse($ids) : $ids;
            //$ids = array_reverse($ids);
            $orderIds = implode(',', $ids);
            $collection->getSelect()->order(new Zend_Db_Expr("FIELD(e.entity_id, $orderIds)"));
            //Mage::log($collection->getSelect()->assemble());
        }
    }

    protected function detectSimilarSort()
    {
        $res = Mage::getSingleton('catalog/session')->getData('sort_order') == $this->getRequestHelper()->getSimilarVarName();
        return $res;
    }

    protected function getSortDirection()
    {
        return Mage::getSingleton('catalog/session')->getData('sort_direction');
    }

    protected function detectCatalogProductCollection($collection)
    {
        $res = $collection instanceof Mage_Catalog_Model_Resource_Product_Collection;
        return $res;
    }

    protected function getDefaultHelper()
    {
        return Mage::helper('morozov_similarity');
    }

    protected function getRequestHelper()
    {
        return Mage::helper('morozov_similarity/request');
    }

    protected function getApiHelper()
    {
        return Mage::helper('morozov_similarity/api');
    }
}

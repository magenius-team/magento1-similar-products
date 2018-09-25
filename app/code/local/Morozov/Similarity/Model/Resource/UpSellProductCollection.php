<?php
class Morozov_Similarity_Model_Resource_UpSellProductCollection extends Mage_Catalog_Model_Resource_Product_Collection
{
    public function setPositionOrder()
    {
        if (Mage::helper('morozov_similarity')->canUse()) {
            $where = $this->getSelect()->getPart(Zend_Db_Select::WHERE);
            foreach($where as &$w) {
                $w = str_replace('`', '', $w);
                preg_match('/e.entity_id[\s]+IN[\s]*\(([^)]+)\)/i', $w, $matches);
                if (isset($matches[1])) {
                    $this->getSelect()->order(new Zend_Db_Expr("FIELD(e.entity_id, {$matches[1]})"));
                }
            }
        }
        return $this;
    }

    public function setIsNotLoaded()
    {
        $this->_isCollectionLoaded = false;
        $this->_items = array();
        return $this;
    }
}

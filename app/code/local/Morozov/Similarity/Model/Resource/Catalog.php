<?php
class Morozov_Similarity_Model_Resource_Catalog
{
    public function getProducts($storeId = 0)
    {
        /*
        $resource = Mage::getSingleton('core/resource');
        $resource->getConnection('core_read');

        $visibilityAttrId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'visibility');
        $statusAttrId     = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'status');
        var_dump($visibilityAttrId);
        var_dump($statusAttrId);

        $visibilityIn = [
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH
        ];
        $visibilityIn = implode(',', $visibilityIn);
        $stockId = 1;     // Default Magento CE uses only 1 stock

        $sql = <<< SQL_PRODUCTS
SELECT
  e.entity_id,
  e.sku,
  IFNULL(visibility.value, visibility_0.value) AS visibility,
  IFNULL(status.value, status_0.value) AS status,
  stock_item.is_in_stock
FROM {$resource->getTableName('catalog_product_entity')} AS e

INNER JOIN {$resource->getTableName('catalog_product_entity_int')} AS visibility_0
ON visibility_0.entity_id = e.entity_id AND visibility_0.attribute_id = $visibilityAttrId AND visibility_0.store_id = 0
LEFT JOIN {$resource->getTableName('catalog_product_entity_int')} AS visibility
ON visibility.entity_id = e.entity_id AND visibility.attribute_id = $visibilityAttrId AND visibility.store_id = $storeId

INNER JOIN {$resource->getTableName('catalog_product_entity_int')} AS status_0
ON status_0.entity_id = e.entity_id AND status_0.attribute_id = $statusAttrId AND status_0.store_id = 0
LEFT JOIN {$resource->getTableName('catalog_product_entity_int')} AS status
ON status.entity_id = e.entity_id AND status.attribute_id = $statusAttrId AND status.store_id = $storeId

INNER JOIN {$resource->getTableName('cataloginventory/stock_item')} AS stock_item
ON stock_item.product_id = e.entity_id

WHERE (visibility_0.value IN ($visibilityIn) OR visibility.value IN ($visibilityIn)) AND stock_item.stock_id = $stockId
SQL_PRODUCTS;
        $res = $resource->getConnection('core_read')->query($sql);
        $rows = $res->fetchAll();
        echo '<pre>';
        var_dump($rows);
        echo '</pre>';
        */
    }
}

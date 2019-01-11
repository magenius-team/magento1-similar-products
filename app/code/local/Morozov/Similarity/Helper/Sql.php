<?php
class Morozov_Similarity_Helper_Sql extends Mage_Core_Helper_Abstract
{
    protected $_imagesPositionOrder = 'ASC';

    public function prepareExportProducts($storeId = 0)
    {
        $resource = Mage::getSingleton('core/resource');
        $visibilityAttrId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'visibility');
        $imageAttrId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'image');

        $sql = <<< SQL
SELECT
  e.entity_id,
  IFNULL(at_visibility.value, at_visibility0.value) AS visibility,
  si.is_in_stock,
  media.images
FROM {$resource->getTableName('catalog_product_entity')} AS e
INNER JOIN {$resource->getTableName('catalog_product_entity_int')} AS at_visibility0
ON (at_visibility0.entity_id = e.entity_id) AND (at_visibility0.attribute_id = $visibilityAttrId) AND (at_visibility0.store_id = 0)
LEFT JOIN {$resource->getTableName('catalog_product_entity_int')} AS at_visibility
ON (at_visibility.entity_id = e.entity_id) AND (at_visibility.attribute_id = $visibilityAttrId) AND (at_visibility.store_id = $storeId)
INNER JOIN (SELECT si.product_id, IF(SUM(is_in_stock) > 0, 1, 0) AS is_in_stock FROM {$resource->getTableName('cataloginventory/stock_item')} AS si GROUP BY si.product_id) AS si
ON si.product_id = e.entity_id

INNER JOIN
(
   -- SELECT
   --   mg.entity_id AS product_id,
   --   GROUP_CONCAT(mg.value ORDER BY IFNULL(mgv.position, mgv0.position) {$this->_imagesPositionOrder}) AS images
   -- FROM {$resource->getTableName('catalog_product_entity_media_gallery')} AS mg
   -- INNER JOIN {$resource->getTableName('catalog_product_entity_media_gallery_value')} AS mgv0
   -- ON (mgv0.value_id = mg.value_id) AND (mgv0.store_id = 0) AND (mgv0.disabled <> 1)
   -- LEFT JOIN {$resource->getTableName('catalog_product_entity_media_gallery_value')} AS mgv
   -- ON (mgv.value_id = mg.value_id) AND (mgv.store_id = $storeId) AND (mgv.disabled <> 1)
   -- GROUP BY mg.entity_id

   SELECT image0.entity_id AS product_id, IFNULL(image.value, image0.value) AS images
   FROM {$resource->getTableName('catalog_product_entity_varchar')} AS image0
   LEFT JOIN {$resource->getTableName('catalog_product_entity_varchar')} AS image
   ON (image.attribute_id = $imageAttrId) AND (image.store_id = $storeId) AND (image.entity_id = image0.entity_id)
   WHERE (image0.attribute_id = $imageAttrId) AND (image0.store_id = 0)
)
AS media ON media.product_id = e.entity_id

HAVING (visibility NOT IN(1))

ORDER BY si.is_in_stock desc, e.entity_id desc
SQL;
        return $sql;
    }
}

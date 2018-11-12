<?php
class Morozov_Similarity_Helper_Sql extends Mage_Core_Helper_Abstract
{
    protected $imagesPositionOrder = 'ASC';

    public function prepareExportProducts()
    {
        $resource = Mage::getSingleton('core/resource');
        $visibilityAttrId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'visibility');

        $sql = <<< SQL
SELECT
  e.entity_id,
  at_visibility_default.value AS visibility,
  si.is_in_stock,
  media.images
FROM {$resource->getTableName('catalog_product_entity')} AS e
INNER JOIN {$resource->getTableName('catalog_product_entity_int')} AS at_visibility_default
ON (at_visibility_default.entity_id = e.entity_id) AND (at_visibility_default.attribute_id = '$visibilityAttrId') AND at_visibility_default.store_id = 0
-- LEFT JOIN `{$resource->getTableName('catalog_product_entity_int')}` AS `at_visibility`
-- ON (`at_visibility`.`entity_id` = `e`.`entity_id`) AND (`at_visibility`.`attribute_id` = '$visibilityAttrId') AND (`at_visibility`.`store_id` = 3)
INNER JOIN (SELECT si.product_id, IF(SUM(is_in_stock) > 0, 1, 0) AS is_in_stock FROM {$resource->getTableName('cataloginventory/stock_item')} AS si GROUP BY si.product_id) AS si
ON si.product_id = e.entity_id

INNER JOIN
(
    SELECT
      mg.entity_id AS product_id,
      GROUP_CONCAT(mg.value ORDER BY mgv.position {$this->imagesPositionOrder}) AS images
    FROM {$resource->getTableName('catalog_product_entity_media_gallery')} AS mg
    INNER JOIN {$resource->getTableName('catalog_product_entity_media_gallery_value')} AS mgv ON mgv.value_id = mg.value_id
    WHERE mgv.store_id = 0
    GROUP BY mg.entity_id
)
AS media ON media.product_id = e.entity_id

WHERE (at_visibility_default.value NOT IN(1))

ORDER BY si.is_in_stock desc, e.entity_id desc
SQL;
        return $sql;
    }
}

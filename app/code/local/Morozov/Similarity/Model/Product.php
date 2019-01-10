<?php
// @codingStandardsIgnoreFile
/**
 * Fixing rewrite conflict with MDN_AdvancedStock
 */
if ((string)Mage::getConfig()->getModuleConfig('MDN_AdvancedStock')->active == 'true') {
    class Morozov_Similarity_Model_Product extends MDN_AdvancedStock_Model_Catalog_Product { }
} else {
    class Morozov_Similarity_Model_Product extends Mage_Catalog_Model_Product { }
}

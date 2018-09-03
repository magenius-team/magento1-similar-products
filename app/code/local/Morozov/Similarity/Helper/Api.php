<?php
class Morozov_Similarity_Helper_Api extends Mage_Core_Helper_Abstract
{
    /**
     * Service ==> Magento
     */
    public function getUpSells($productId)
    {
        if (Mage::helper('morozov_similarity')->isDummy()) {
            $productId = 3923; // dummy
        }

        $url = $this->getDefaultHelper()->getUrl() . 'api/view/' . $productId;
        //Mage::log($url);
        $response = file_get_contents($url);
        $response = str_replace("NaN", '"NaN"', $response);
        $items = Zend_Json::decode($response);
        //Mage::log($data);
        $tempArr = [];
        foreach ($items as $item) {
            if ($item[1] > 0.0000001) {
                $tempArr[$item[0][0]['entity_id']] = $item[0][0]['image'] . $item[1];
            }
        }
        $ids = array_keys(array_unique($tempArr));
        return $ids;
    }

    /**
     * Service <== Magento
     */
    public function setAllProducts()
    {
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('visibility')
            ->addAttributeToFilter('visibility', ['nin' => [
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE
            ]])
        ;
        $this->getDefaultHelper()->log('setAllProducts ' . count($collection) . ' products');
        $rows = [];
        foreach($collection as $product) {
            //Mage::log($product->getEntityId());
            $attributeCode = 'media_gallery';
            $attribute = $product->getResource()->getAttribute($attributeCode);
            $backend = $attribute->getBackend();
            $backend->afterLoad($product);
            $g = $product->getData('media_gallery');
            if (count($g['images'])) {
                usort($g['images'], 'Morozov_Similarity_Helper_Api::cmpImages');
                $image = $g['images'][0];
                $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $image['file'];
                $rows[]= [$product->getEntityId(), $url];
            }
            /*
            $mediaGallery = $product->getMediaGalleryImages();
            if (count($mediaGallery)) {
                //Mage::log($product->getEntityId());
            }
            */
        }

        $csvDir = $this->getDefaultHelper()->getExportDir();
        if (!is_dir($csvDir)) {
            mkdir($csvDir);
        }
        $f = fopen($this->getDefaultHelper()->getProductsFile(), 'a+');
        foreach($rows as $row) {
            fputcsv($f, $row);
        }
        fclose($f);
    }

    public static function cmpImages($a, $b)
    {
        return (int)$a['position_default'] >= (int)$b['position_default'];
    }

    protected function getDefaultHelper()
    {
        return Mage::helper('morozov_similarity');
    }
}

<?php
class Morozov_Similarity_Helper_Api extends Mage_Core_Helper_Abstract
{
    const CHECK_IMAGE_FILE_EXISTS = true;

    protected $csvColumns = [
        'product_id',
        'is_in_stock',
        'image'
    ];

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

    protected function collectProducts()
    {
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('visibility')
            ->addAttributeToFilter('visibility', ['nin' => [
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE
            ]])
        ;
        $resource = Mage::getSingleton('core/resource');
        $selectInStock = $resource->getConnection('core_read')->select();
        $selectInStock
            ->from(['si' => 'mage_cataloginventory_stock_item'], [
                'product_id' => 'si.product_id',
                'is_in_stock' => new Zend_Db_Expr('IF(SUM(is_in_stock)>0, 1, 0)')
            ])
            ->group('si.product_id')
        ;

        $collection->getSelect()
            ->joinInner(
                ['si' => $selectInStock],
                'si.product_id = e.entity_id',
                'is_in_stock'
            )
        ;
        //Mage::log($collection->getSelect()->assemble());

        $this->getDefaultHelper()->log('Export ' . count($collection) . ' products');
        $rows = [];
        $rows[]= $this->csvColumns;
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
                if (self::CHECK_IMAGE_FILE_EXISTS) {
                    $fileExists = file_exists(Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $image['file']);
                    if (!$fileExists) {
                        continue;
                    }
                }

                $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $image['file'];
                $rows[]= [
                    $product->getEntityId(),
                    $product->getIsInStock(),
                    $url
                ];
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
            if (!mkdir($csvDir)) {
                throw new Exception('Failed to create export directory..');
            }
        }
        if (!$f = fopen($this->getDefaultHelper()->getProductsFile(), 'w+')) {
            throw new Exception('Failed to create export Products file..');
        }
        foreach($rows as $row) {
            fputcsv($f, $row);
        }
        fclose($f);
    }

    /**
     * Service <== Magento
     */
    public function setAllProducts()
    {
        $this->collectProducts();

        //@TODO: send CSV file to service
        $url = $this->getDefaultHelper()->getUrl() . 'api/reindex';

        /*
        $client = new Zend_Http_Client($url, [
            //'timeout' => (int)$this->getDefaultHelper()->getTimeout()  // in seconds only
        ]);
        $data = [
            'key'  => $this->getDefaultHelper()->getKey(),
            'file' => $this->getDefaultHelper()->getProductsFileUrl()
        ];
        $client
            ->setRawData(Zend_Json::encode($data))
        ;
        $response = $client->request('POST');
        if ($response->getStatus() != 200) {
            throw new Exception($url . ':  ' . $response->getMessage());
        }
        */

        $data = [
            'key' => $this->getDefaultHelper()->getKey(),
            'file' => $this->getDefaultHelper()->getProductsFileUrl()
        ];
        $json = Zend_Json::encode($data);
        //Mage::log($json);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ]);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1); // for linux
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, (int)$this->getDefaultHelper()->getTimeout());
        $result = curl_exec($ch);

        $info = curl_getinfo($ch);
        $error = curl_errno($ch);  // 28 - timeout
        $this->getDefaultHelper()->log($url);
        $this->getDefaultHelper()->log($info['http_code']);
        $this->getDefaultHelper()->log($result);
        if ($error) {
            $message = curl_error($ch);
            throw new Exception($error . ' ' . $message);
        }

        curl_close($ch);
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

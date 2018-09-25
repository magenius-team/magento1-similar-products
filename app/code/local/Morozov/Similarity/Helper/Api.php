<?php
class Morozov_Similarity_Helper_Api extends Mage_Core_Helper_Abstract
{
    const CHECK_IMAGE_FILE_EXISTS = true;

    const MASTER_URL       = 'https://master.similarity.morozov.group/';
    const PATH_REGIONS     = 'api/regions';

    const PATH_GET_UPSELLS = 'api/view/%s';
    const PATH_REINDEX     = 'api/reindex';

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
        $url = $this->getDefaultHelper()->getUrl() . sprintf(self::PATH_GET_UPSELLS, $productId);
        $response = file_get_contents($url);
        $response = str_replace("NaN", '"NaN"', $response);
        $items = Zend_Json::decode($response);
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
        $csvDir = $this->getDefaultHelper()->getExportDir();
        if (!is_dir($csvDir)) {
            if (!mkdir($csvDir)) {
                throw new Exception('Failed to create export directory..');
            }
        }
        if (!$f = fopen($this->getDefaultHelper()->getProductsFile(), 'w+')) {
            throw new Exception('Failed to create export Products file..');
        }
        fputcsv($f, $this->csvColumns);

        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $res = $read->query($this->getSqlHelper()->prepareExportProducts());
        if ($res) {
            $count = 0;
            while($row = $res->fetch(PDO::FETCH_ASSOC)) {
                $images = explode(',', $row['images']);
                $image = $images[0];
                if (self::CHECK_IMAGE_FILE_EXISTS) {
                    $fileExists = file_exists(Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $image);
                    if (!$fileExists) {
                        continue;
                    }
                }
                $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $image;
                $csvRow = [
                    $row['entity_id'],
                    $row['is_in_stock'],
                    $url
                ];
                fputcsv($f, $csvRow);
                $count++;
            }
            $this->getDefaultHelper()->log("Exported  $count  products");
        } else {
            throw new Exception('Failed to execute SQL..');
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
        $url = $this->getDefaultHelper()->getUrl() . self::PATH_REINDEX;
        $data = [
            'key' => $this->getDefaultHelper()->getKey(),
            'file' => $this->getDefaultHelper()->getProductsFileUrl()
        ];
        $json = Zend_Json::encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ]);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, (int)$this->getDefaultHelper()->getTimeout());
        $result = curl_exec($ch);

        $info = curl_getinfo($ch);
        $error = curl_errno($ch);
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

    public function getNearestRegion()
    {
        $config = file_get_contents(self::MASTER_URL . self::PATH_REGIONS);

        $now = function () {
            return time() + microtime(true);
        };

        $distances = json_decode($config, true);
        array_walk($distances, function (&$region, $url) use ($now) {
            $start = $now();
            file_get_contents($region);
            $region = number_format($now() - $start, 6);
        });

        asort($distances);
        reset($distances);
        $nearestRegion = key($distances);
        return $nearestRegion;
    }

    protected function getDefaultHelper()
    {
        return Mage::helper('morozov_similarity');
    }

    protected function getSqlHelper()
    {
        return Mage::helper('morozov_similarity/sql');
    }
}

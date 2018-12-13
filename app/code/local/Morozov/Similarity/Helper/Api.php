<?php
class Morozov_Similarity_Helper_Api extends Mage_Core_Helper_Abstract
{
    const MASTER_URL       = 'https://master.similarity.morozov.group/';
    const PATH_REGIONS     = 'api/regions';

    const PATH_GET_UPSELLS = 'api/view/%s';
    const PATH_REINDEX     = 'api/reindex';

    /**
     * Service ==> Magento
     */
    public function getUpSells($productId)
    {
        $url = $this->getDefaultHelper()->getUrl() . sprintf(self::PATH_GET_UPSELLS, $productId);
        $ctxParams = [];
        $toSec = $this->getDefaultHelper()->getTimeoutSec();
        if ($toSec > 0.0) {
            $ctxParams = [
                'http' => [
                    'timeout' => $toSec,  // In seconds (float), example 0.2, 0.5...
                ]
            ];
        }
        $ctx = stream_context_create($ctxParams);
        if (!$response = @file_get_contents($url, false, $ctx)) {
            throw new Exception($url . ' empty response');
            //return [];
        }
        $response = str_replace("NaN", '"NaN"', $response);
        $items = Zend_Json::decode($response);  // error
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
        //$this->collectProducts();
        $this->getProductHelper()->collect();

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
        $this->getDefaultHelper()->log($info['http_code'] . ' ' . str_replace(["\n", "\r"], ['', ''], $result));
        if ($error) {
            $message = curl_error($ch);
            throw new Exception($error . ' ' . $message);
        }

        curl_close($ch);
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

    protected function getProductHelper()
    {
        return Mage::helper('morozov_similarity/product');
    }
}

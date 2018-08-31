<?php
class Morozov_Similarity_Helper_Api extends Mage_Core_Helper_Abstract
{
    public function getUpSells($productId)
    {
        $productId = 3923; // dummy

        $url = $this->getDefaultHelper()->getUrl() . $productId;
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

    public function setAllProducts()
    {

    }

    protected function getDefaultHelper()
    {
        return Mage::helper('morozov_similarity');
    }
}

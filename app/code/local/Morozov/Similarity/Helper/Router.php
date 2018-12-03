<?php
class Morozov_Similarity_Helper_Router extends Mage_Core_Helper_Abstract
{
    public function getProductIdByUrl($url)
    {
        if ($urlKey = $this->getUrlKey($url)) {
            $product = Mage::getModel('catalog/product')->loadByAttribute('url_key', $urlKey);
            $id = $product ? $product->getEntityId() : null;
            return $id;
        }

        return null;
    }

    public function getUrlByProductId()
    {

    }

    protected function getUrlKey($url)
    {
        preg_match_all("/\/([^\/]+)\.html/i", $url, $matches);
        $urlKey = @$matches[1][0];
        return $urlKey;
    }
}

<?php
class Morozov_Similarity_Helper_Data extends Mage_Core_Helper_Abstract
{
    const LOG_FILE      = 'morozov_similarity.log';

    const PATH_ENABLED  = 'morozov_similarity/general/enabled';
    const PATH_EMAIL    = 'morozov_similarity/general/email';
    const PATH_URL      = 'morozov_similarity/general/url';
    const PATH_KEY      = 'morozov_similarity/general/key';
    const PATH_TIMEOUT  = 'morozov_similarity/general/timeout';
    const PATH_CRON_ENABLED        = 'morozov_similarity/general/cron_enabled';
    const PATH_IMAGE_CHECK_ENABLED = 'morozov_similarity/general/image_check_enabled';

    const PATH_UPSELL_MAXCOUNT = 'morozov_similarity/upsell_options/upsell_max_count';

    const EXPORT_DIR    = 'morozov_similarity';
    const PRODUCTS_FILE = 'products.csv';

    public function log($message)
    {
        Mage::log($message, null, self::LOG_FILE);
    }

    public function getIsEnabled($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::PATH_ENABLED, $storeId);
    }

    public function getEmail($storeId = null)
    {
        return Mage::getStoreConfig(self::PATH_EMAIL, $storeId);
    }

    public function getUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::PATH_URL, $storeId);
    }

    public function getKey($storeId = null)
    {
        return Mage::getStoreConfig(self::PATH_KEY, $storeId);
    }

    public function getTimeout($storeId = null)
    {
        return Mage::getStoreConfig(self::PATH_TIMEOUT, $storeId);
    }

    public function getCronEnabled($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::PATH_CRON_ENABLED, $storeId);
    }

    public function getImageCheckEnabled($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::PATH_IMAGE_CHECK_ENABLED, $storeId);
    }

    public function getUpSellMaxCount($storeId = null)
    {
        return (int)Mage::getStoreConfig(self::PATH_UPSELL_MAXCOUNT, $storeId);
    }

    public function canUse()
    {
        $res = $this->getIsEnabled() && $this->getUrl() && $this->getKey();
        $res = $res && (!Mage::app()->getStore()->isAdmin());
        return $res;
    }

    public function getExportDir()
    {
        $dir = Mage::getBaseDir('media') . DS . self::EXPORT_DIR;
        return $dir;
    }

    public function getProductsFile()
    {
        $file = $this->getExportDir() . DS . self::PRODUCTS_FILE;
        return $file;
    }

    public function getProductsFileUrl()
    {
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . self::EXPORT_DIR . '/' . self::PRODUCTS_FILE;
        return $url;
    }
}

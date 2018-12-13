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

    protected $storeId;

    public function log($message)
    {
        Mage::log($message, null, self::LOG_FILE);
    }

    public function getIsEnabled()
    {
        return Mage::getStoreConfigFlag(self::PATH_ENABLED, $this->getStoreId());
    }

    public function getEmail()
    {
        return Mage::getStoreConfig(self::PATH_EMAIL, $this->getStoreId());
    }

    public function getUrl()
    {
        //Mage::log('getIsEnabled');
        //Mage::log($this->storeId);
        return Mage::getStoreConfig(self::PATH_URL, $this->getStoreId());
    }

    public function getKey()
    {
        return Mage::getStoreConfig(self::PATH_KEY, $this->getStoreId());
    }

    public function getTimeout()
    {
        return Mage::getStoreConfig(self::PATH_TIMEOUT, $this->getStoreId());
    }

    public function getTimeoutSec()
    {
        $to = (float)(((int)$this->getTimeout()) / 1000.0);
        return $to;
    }

    public function getCronEnabled()
    {
        return Mage::getStoreConfigFlag(self::PATH_CRON_ENABLED, $this->getStoreId());
    }

    public function getImageCheckEnabled()
    {
        return Mage::getStoreConfigFlag(self::PATH_IMAGE_CHECK_ENABLED, $this->getStoreId());
    }

    public function getUpSellMaxCount()
    {
        return (int)Mage::getStoreConfig(self::PATH_UPSELL_MAXCOUNT, $this->getStoreId());
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

    protected function getProductsFileName()
    {
        $filename = $this->getStoreId() ? "products_{$this->getStoreId()}.csv" : 'products.csv';
        return $filename;
    }

    public function getProductsFile()
    {
        $file = $this->getExportDir() . DS . $this->getProductsFileName();
        return $file;
    }

    public function getProductsFileUrl()
    {
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . self::EXPORT_DIR . '/' . $this->getProductsFileName();
        return $url;
    }

    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }

    public function getStoreId()
    {
        return $this->storeId;
    }

    public function getStores()
    {
        $stores = Mage::app()->getStores();
        return $stores;
    }
}

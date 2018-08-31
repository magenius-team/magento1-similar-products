<?php
class Morozov_Similarity_Helper_Data extends Mage_Core_Helper_Abstract
{
    const LOG_FILE      = 'morozov_similarity.log';

    const PATH_ENABLED  = 'morozov_similarity/general/enabled';
    const PATH_URL      = 'morozov_similarity/general/url';
    const PATH_EMAIL    = 'morozov_similarity/general/email';
    const PATH_PASSWORD = 'morozov_similarity/general/password';

    const PATH_UPSELL_MAXCOUNT = 'morozov_similarity/upsell_options/upsell_max_count';

    public function log($message)
    {
        Mage::log($message, null, self::LOG_FILE);
    }

    public function getIsEnabled()
    {
        return Mage::getStoreConfigFlag(self::PATH_ENABLED);
    }

    public function getUrl()
    {
        return Mage::getStoreConfig(self::PATH_URL);
    }

    public function getEmail()
    {
        return Mage::getStoreConfig(self::PATH_EMAIL);
    }

    public function getPassword()
    {
        return Mage::getStoreConfig(self::PATH_PASSWORD);
    }

    public function getUpSellMaxCount()
    {
        return (int)Mage::getStoreConfig(self::PATH_UPSELL_MAXCOUNT);
    }

    public function canUse()
    {
        $res = $this->getIsEnabled() && $this->getUrl() && $this->getEmail() && $this->getPassword();
        $res = $res && (!Mage::app()->getStore()->isAdmin());

        //return false;

        return $res;
    }

    public function isDummy()
    {
        return true;
    }
}

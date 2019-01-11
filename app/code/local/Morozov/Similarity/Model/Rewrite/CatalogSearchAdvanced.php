<?php
class Morozov_Similarity_Model_Rewrite_CatalogSearchAdvanced extends Mage_CatalogSearch_Model_Advanced
{
    public function addFilters($values)
    {
        try {
            parent::addFilters($values);
            if ($similar = $this->getRequestHelper()->getSimilar()) {
                $this->addSimilarFilters($similar);
            }
        } catch (Mage_Core_Exception $e) {
            if (($similar = $this->getRequestHelper()->getSimilar()) && $this->detectTermsNotSpecifiedMsg($e->getMessage())) {
                $this->addSimilarFilters($similar);
            } else {
                throw $e;
            }
        }

        return $this;
    }

    protected function detectTermsNotSpecifiedMsg($message)
    {
        $res = stristr($message, Mage::helper('catalogsearch')->__('Please specify at least one search term.'));
        return $res;
    }

    protected function addSimilarFilters($similar)
    {
        $ids = array();
        try {
            if ($ids = @$this->getApiHelper()->getUpSells((int)$similar)) {
                $this->getProductCollection()
                    ->addFieldToFilter('entity_id', array('in' => $ids));
            }
        } catch (Exception $e) {
            $this->getDefaultHelper()->log('Advanced Search: ' . $e->getMessage());
        }

        if (!$ids) {
            throw new Mage_Core_Exception("Couldn't get similar products from the service..");
        }

    }

    protected function getRequestHelper()
    {
        return Mage::helper('morozov_similarity/request');
    }

    protected function getApiHelper()
    {
        return Mage::helper('morozov_similarity/api');
    }

    protected function getDefaultHelper()
    {
        return Mage::helper('morozov_similarity');
    }
}

<?php
class Morozov_Similarity_Model_Observer_Block
{
    public function onToHtmlBefore($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($this->detectCatalogProductList($block) && $this->detectCategoryViewPage()) {
            if ($block->getParentBlock() instanceof  Mage_Catalog_Block_Category_View) {
                // is not working for filtering products within a category..
            }
            if ($similar = $this->getRequestHelper()->getSimilar()) {
                try {
                    if ($ids = @$this->getApiHelper()->getUpSells((int)$similar)) {
                        $block->getLoadedProductCollection()->addFieldToFilter('entity_id', ['in' => $ids]);
                        return;
                    }
                } catch (Exception $e) {
                    $this->getDefaultHelper()->log('Category: ' . $e->getMessage());
                }
                $block->getLoadedProductCollection()->addFieldToFilter('entity_id', null);
            }
        }
    }

    protected function detectCatalogProductList($block)
    {
        $res = $block instanceof Mage_Catalog_Block_Product_List;
        return $res;
    }

    protected function detectCategoryViewPage()
    {
        //@TODO: add other Request URIs to process more product list pages
        if (stristr(Mage::app()->getRequest()->getRequestUri(), '/catalog/category/view')) {
            return true;
        }

        return false;
    }

    public function onToHtmlAfter($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $transport = $observer->getEvent()->getTransport();
        if ($this->detectAdvancedSearchForm($block)) {
            $html = $transport->getHtml();
            $html = $this->injectSimilarFormInput($block, $html);
            $transport->setHtml($html);
        }
    }

    protected function injectSimilarFormInput($block, $html)
    {
        if ($similar = $this->getRequestHelper()->getSimilar()) {
            $url = str_replace(['/'], ['\/'], $block->getSearchPostUrl());
            $html = preg_replace(
                "/(<form(.)+($url)(.)+>)/i",
                "$1" . $this->getRequestHelper()->getSimilarFormInput($similar),
                $html
            );
        }
        return $html;
    }

    protected function detectAdvancedSearchForm($block)
    {
        $res = $block instanceof Mage_CatalogSearch_Block_Advanced_Form;
        return $res;
    }

    protected function getDefaultHelper()
    {
        return Mage::helper('morozov_similarity');
    }

    protected function getRequestHelper()
    {
        return Mage::helper('morozov_similarity/request');
    }

    protected function getApiHelper()
    {
        return Mage::helper('morozov_similarity/api');
    }
}

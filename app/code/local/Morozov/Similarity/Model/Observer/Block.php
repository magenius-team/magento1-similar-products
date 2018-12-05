<?php
class Morozov_Similarity_Model_Observer_Block
{
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

    protected function getRequestHelper()
    {
        return Mage::helper('morozov_similarity/request');
    }
}
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

                        if ($this->getDefaultHelper()->getCustomSortByForCategoryEnabled()) {
                            $this->setSortByForCategory($block);
                            if ($toolbar = $block->getToolbarBlock()) {
                                $toolbar->setCollection($block->getLoadedProductCollection());
                            }
                        }
                    }
                } catch (Exception $e) {
                    $this->getDefaultHelper()->log('Category: ' . $e->getMessage());
                }
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

    protected function setSortByForCategory($productListBlock)
    {
        if ($productListBlock->getParentBlock()) {
            if ($category = $productListBlock->getParentBlock()->getCurrentCategory()) {
                $sortBy = $category->getAvailableSortByOptions();
                $sortBy[$this->getRequestHelper()->getSimilarVarName()] = $this->getRequestHelper()->getSimilarLabel();
                $productListBlock->setAvailableOrders($sortBy);
                if ($toolbar = $productListBlock->getToolbarBlock()) {
                    $toolbar->setAvailableOrders($sortBy);

                    if (!Mage::getSingleton('catalog/session')->getData('sort_order')) {
                        $productListBlock
                            ->setSortBy($this->getRequestHelper()->getSimilarVarName())
                            //->setDefaultDirection('desc')
                        ;
                        //$toolbar->setDefaultOrder($this->getRequestHelper()->getSimilarVarName());
                        //$toolbar->setDefaultDirection('asc');

                        //Mage::getSingleton('catalog/session')
                        //    ->setData('sort_order', $this->getRequestHelper()->getSimilarVarName())
                        //    ->setData('sort_direction', 'asc')
                        //;
                        $toolbar->setData('_current_grid_order', $this->getRequestHelper()->getSimilarVarName());
                        //$toolbar->setData('_current_grid_direction', 'desc');
                    }
                }
            }
        }
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

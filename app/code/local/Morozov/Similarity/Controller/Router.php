<?php
class Morozov_Similarity_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    const FRONT_NAME = 'similar';

    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            return false;
        }

        if (!$this->getFrontName()) {
            return false;
        }

        if (stristr($request->getPathInfo(), '/' . $this->getFrontName())) {
            if ($productId = $this->getRouterHelper()->getProductIdByUrl($request->getPathInfo())) {
                $request
                    ->setModuleName('catalogsearch')
                    ->setControllerName('advanced')
                    ->setActionName('result');
                $request
                    ->setQuery(
                        array(
                        'similar' => $productId,
                        // 'order'       => 'name',
                        // 'dir'         => 'asc',
                        // 'name'        => 'apron',
                        // 'description' => 'descriptionnn',
                        // 'sku'         => 'xxxxx',
                        // 'price[from]' => '10',
                        // 'price[to]'   => '100',
                        )
                    );
                return true;
            } else {
                $this->getDefaultHelper()->log('Router: failed to load Product by URL ' . $request->getPathInfo());
            }
        }

        return false;
    }

    public function getFrontName()
    {
        return self::FRONT_NAME;
    }

    public function getDefaultHelper()
    {
        return Mage::helper('morozov_similarity');
    }

    public function getRouterHelper()
    {
        return Mage::helper('morozov_similarity/router');
    }
}
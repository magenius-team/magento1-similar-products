<?php
class Morozov_Similarity_Model_Observer_Controller
{
    public function initControllerRouters($observer)
    {
        /** @var Mage_Core_Controller_Varien_Front */
        $front = $observer->getEvent()->getFront();
        $router = new Morozov_Similarity_Controller_Router();
        $front->addRouter($router->getFrontName(), $router);
    }

    public function onFrontInitBefore($observer)
    {
        $front = $observer->getEvent()->getFront();
        //Mage::log('iiii');
        //Mage::log(Mage::app()->getRequest()->getParams());
    }
}

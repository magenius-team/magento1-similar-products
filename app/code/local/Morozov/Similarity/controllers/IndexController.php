<?php
class Morozov_Similarity_IndexController
extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        var_dump(Mage::helper('morozov_similarity')->getTimeout());

        $url = 'https://bragard-similarity.apps.msk.morozov.cloud/api/view/1845';
        $client = new Zend_Http_Client($url, ['timeout' => 1]);
        $data = [];
        $client
            ->setRawData(Zend_Json::encode($data))
        ;
        $response = $client->request('GET');
        echo '<pre>';
        var_dump($response);  // 200
        //var_dump($response->getMessage()); // OK

        echo '</pre>';
        //Mage::helper('morozov_similarity/api')->setAllProducts();

        //Mage::getResourceModel('morozov_similarity/catalog')->getProducts(3);
        /*
        var_dump(Mage::helper('morozov_similarity')->getIsEnabled());
        var_dump(Mage::helper('morozov_similarity')->getUrl());
        var_dump(Mage::helper('morozov_similarity')->getEmail());
        var_dump(Mage::helper('morozov_similarity')->getPassword());

        var_dump(Mage::helper('morozov_similarity')->getUpSellMaxCount());
        */
    }
}




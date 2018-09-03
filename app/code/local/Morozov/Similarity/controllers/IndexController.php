<?php
class Morozov_Similarity_IndexController
extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        //Mage::helper('morozov_similarity/api')->setAllProducts();
        //var_dump((int)Mage::helper('morozov_similarity')->getTimeout());

        //ini_set('always_populate_raw_post_data', '-1');
        //$url = 'https://bragard-similarity.apps.msk.morozov.cloud/api/view/1845';
        $url = 'http://bragardusa.l/morozov_similarity/index/test';
        //$url = 'http://mail.ru';

        $data = ['key' => '1233444', 'file' => 'http://mail.ru/file'];
        $json = Zend_Json::encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100);
        $result = curl_exec($ch);


        //echo '<pre>';
        $info = curl_getinfo($ch);
        $error = curl_errno($ch); // 28 -timeout
        $message = curl_error($ch);
        Mage::log($error);
        Mage::log($message);
        Mage::log($info);
        //var_dump($info['http_code']);
        //var_dump($info);
        //var_dump($result);
        Mage::log($result);
        //echo '</pre>';

        curl_close($ch);
        exit;
        //var_dump(Mage::helper('morozov_similarity')->getTimeout());


        /*
        //$url = 'https://bragard-similarity.apps.msk.morozov.cloud/api/view/1845';
        $url = 'http://bragardusa.l/morozov_similarity/index/test';
        $client = new Zend_Http_Client($url, ['timeout' => 10]);
        $data = [];
        $client
            ->setRawData(Zend_Json::encode($data))
        ;
        $response = $client->request('POST');
        // echo '<pre>';
        //var_dump($response);  // 200
        //var_dump($response->getMessage()); // OK
        //echo '</pre>';
        //Mage::helper('morozov_similarity/api')->setAllProducts();
        */


        //Mage::getResourceModel('morozov_similarity/catalog')->getProducts(3);
        /*
        var_dump(Mage::helper('morozov_similarity')->getIsEnabled());
        var_dump(Mage::helper('morozov_similarity')->getUrl());
        var_dump(Mage::helper('morozov_similarity')->getEmail());
        var_dump(Mage::helper('morozov_similarity')->getPassword());

        var_dump(Mage::helper('morozov_similarity')->getUpSellMaxCount());
        */
    }

    public function testAction()
    {
        //ini_set('always_populate_raw_post_data', '-1');
        //Mage::log($this->getRequest()->getRawBody());

    }
}




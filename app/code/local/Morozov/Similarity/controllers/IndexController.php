<?php
class Morozov_Similarity_IndexController
extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        Mage::getResourceModel('morozov_similarity/catalog')->getProducts(3);
        /*
        var_dump(Mage::helper('morozov_similarity')->getIsEnabled());
        var_dump(Mage::helper('morozov_similarity')->getUrl());
        var_dump(Mage::helper('morozov_similarity')->getEmail());
        var_dump(Mage::helper('morozov_similarity')->getPassword());

        var_dump(Mage::helper('morozov_similarity')->getUpSellMaxCount());
        */
    }
}




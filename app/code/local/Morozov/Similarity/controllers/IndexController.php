<?php
class Morozov_Similarity_IndexController
extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {

    }

    public function setCredsAction()
    {
        if (!$this->canSetCreds()) {
            return;
        }

        if ($url = $this->getRequest()->getParam('url')) {
            Mage::getModel('core/config')->saveConfig(
                Morozov_Similarity_Helper_Data::PATH_URL,
                $url
            );
        }
    }

    protected function canSetCreds()
    {
        return true;
    }
}




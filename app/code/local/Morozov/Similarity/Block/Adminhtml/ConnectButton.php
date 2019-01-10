<?php
class Morozov_Similarity_Block_Adminhtml_ConnectButton
extends Morozov_Similarity_Block_Adminhtml_AjaxButton
{
    protected $_uniqId = 'connectbutton';

    protected $_label = 'Connect';

    public function getAjaxSyncUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('*/similarity/connect');
    }
}
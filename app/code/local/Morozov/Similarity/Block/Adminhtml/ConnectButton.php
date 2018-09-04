<?php
class Morozov_Similarity_Block_Adminhtml_ConnectButton
extends Morozov_Similarity_Block_Adminhtml_AjaxButton
{
    protected $uniqId = 'connectbutton';

    protected $label = 'Connect';

    public function getAjaxSyncUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('*/similarity/connect');
    }
}
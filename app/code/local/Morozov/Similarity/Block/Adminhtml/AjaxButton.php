<?php
class Morozov_Similarity_Block_Adminhtml_AjaxButton
extends Mage_Adminhtml_Block_System_Config_System_Storage_Media_Synchronize
{
    protected $_uniqId = 'ajaxbutton';

    protected $_label = 'Button Label';

    protected function _construct()
    {
        //parent::_construct();
        $this->setTemplate(null);
    }

    protected function getUniqId()
    {
        return $this->_uniqId;
    }

    protected function getLabel()
    {
        return $this->_label;
    }

    protected function getJsMethod()
    {
        return $this->getUniqId() . 'Request';
    }

    public function getAjaxSyncUrl()
    {
        //@TODO: paste your URL here
        return Mage::getSingleton('adminhtml/url')->getUrl('*/*/*');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'        => $this->getUniqId(),
                'label'     => $this->getLabel(),
                'class'     => 'save',
                'onclick'   => "{$this->getJsMethod()}(); return false;"
            ))
        ;
        return $button->toHtml();
    }

    protected function getJs()
    {
        $js = <<< JAVASCRIPT
<script type="text/javascript">
function {$this->getJsMethod()}() {
    new Ajax.Request('{$this->getAjaxSyncUrl()}', {
        asynchronous: true
    });
}
</script>
JAVASCRIPT;
        return $js;
    }

    protected function _toHtml()
    {
        $html = $this->getButtonHtml() . $this->getJs();
        return $html;
    }
}
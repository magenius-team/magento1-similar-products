<?php
class Morozov_Similarity_Helper_Request extends Mage_Core_Helper_Abstract
{
    protected $similarVarName = 'similar';

    protected $similarLabel = 'Similar';

    public function getSimilarVarName()
    {
        return $this->similarVarName;
    }

    public function getSimilarLabel()
    {
        return $this->similarLabel;
    }

    public function getSimilar()
    {
        $similar = Mage::app()->getRequest()->getParam($this->getSimilarVarName());
        return $similar;
    }

    public function getSimilarFormInput($value = '')
    {
        $input = '';
        if ($value) {
            $input = '<input type="hidden" name="' . $this->getSimilarVarName() . '" value="' . $value . '"/>';
        }

        return $input;
    }
}

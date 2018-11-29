<?php
class Morozov_Similarity_Helper_AdvancedSearch extends Mage_Core_Helper_Abstract
{
    protected $similarVarName = 'similar';

    public function getSimilarVarName()
    {
        return $this->similarVarName;
    }

    public function getSimilarFormInput()
    {
        $input = '';
        if ($value = Mage::app()->getRequest()->getParam($this->getSimilarVarName())) {
            $input = '<input type="hidden" name="' . $this->getSimilarVarName() . '" value="' . $value . '"/>';
        }
        return $input;
    }
}

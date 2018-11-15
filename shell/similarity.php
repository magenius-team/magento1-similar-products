<?php
require_once 'abstract.php';

class Morozov_Similarity_Shell extends Mage_Shell_Abstract
{
    protected function getDefaultHelper()
    {
        return Mage::helper('morozov_similarity');
    }

    protected function getApiHelper()
    {
        return Mage::helper('morozov_similarity/api');
    }

    /**
     * Run script
     *
     */
    public function run()
    {
        if ($this->getArg('reindexall')) {
            foreach($this->getDefaultHelper()->getStores() as $store) {
                try {
                    $msg = "Pushing Products to the service (Store ID = {$store->getStoreId()}): ";
                    echo "\n" . $msg;
                    $this->getDefaultHelper()->log('');
                    $this->getDefaultHelper()->log($msg);
                    $this->getDefaultHelper()->setStoreId($store->getStoreId());
                    $this->getApiHelper()->setAllProducts();
                    $msg = 'Done.';
                    echo $msg;
                    $this->getDefaultHelper()->log($msg);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    $this->getDefaultHelper()->log($e->getMessage());
                }
            }
        } else if ($this->getArg('help')) {
            echo $this->usageHelp();
        } else {
            echo $this->usageHelp();
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f similarity.php [options]
  reindexall                    Push all Products to the service
  help                          This help
USAGE;
    }
}

$shell = new Morozov_Similarity_Shell();
$shell->run();

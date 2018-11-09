<?php
require_once 'abstract.php';

class Morozov_Similarity_Shell extends Mage_Shell_Abstract
{
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
            try {
                $this->getApiHelper()->setAllProducts();
            } catch (Exception $e) {
                echo $e->getMessage();
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

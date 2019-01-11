<?php
class Morozov_Similarity_Helper_Product extends Mage_Core_Helper_Abstract
{
    const CHECK_IMAGE_FILE_EXISTS = true;

    protected $csvColumns = array(
        'entity_id',
        'is_in_stock',
        'image'
    );

    public function collect()
    {
        $csvDir = $this->getDefaultHelper()->getExportDir();
        if (!is_dir($csvDir)) {
            if (!mkdir($csvDir)) {
                throw new Exception('Failed to create export directory..');
            }
        }

        if (!$f = fopen($this->getDefaultHelper()->getProductsFile(), 'w+')) {
            throw new Exception('Failed to create export Products file..');
        }

        fputcsv($f, $this->csvColumns);

        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $res = $read->query($this->getSqlHelper()->prepareExportProducts((int)$this->getDefaultHelper()->getStoreId()));
        if ($res) {
            $count = 0;
            while($row = $res->fetch(PDO::FETCH_ASSOC)) {
                $images = explode(',', $row['images']);
                $image = $images[0];
                if ($this->getDefaultHelper()->getImageCheckEnabled()) {
                    $isFile = is_file(Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $image);
                    if (!$isFile) {
                        continue;
                    }
                }

                $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $image;
                $csvRow = array(
                    $row['entity_id'],
                    $row['is_in_stock'],
                    $url
                );
                fputcsv($f, $csvRow);
                $count++;
            }

            $this->getDefaultHelper()->log("Total Products saved to disk: $count");
        } else {
            throw new Exception('Failed to execute SQL..');
        }

        fclose($f);
    }

    protected function getDefaultHelper()
    {
        return Mage::helper('morozov_similarity');
    }

    protected function getSqlHelper()
    {
        return Mage::helper('morozov_similarity/sql');
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Downloadable_Model_Observer (duplicate downloadable data)
 */
class Mage_Downloadable_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Downloadable/_files/product_with_files.php
     */
    public function testDuplicateProductDownloadableProductWithFilesSuccessfullyDuplicated()
    {
        $currentProduct = new Mage_Catalog_Model_Product;
        $currentProduct->load(1); // fixture for initial product
        $currentLinks = $currentProduct->getTypeInstance($currentProduct)->getLinks($currentProduct);
        $currentSamples = $currentProduct->getTypeInstance($currentProduct)->getSamples($currentProduct);

        $newProduct = $currentProduct->duplicate();

        $newLinks = $newProduct->getTypeInstance($newProduct)->getLinks($newProduct);
        $newSamples = $newProduct->getTypeInstance($newProduct)->getSamples($newProduct);

        $this->assertEquals($currentLinks, $newLinks);
        $this->assertEquals($currentSamples, $newSamples);
    }
}

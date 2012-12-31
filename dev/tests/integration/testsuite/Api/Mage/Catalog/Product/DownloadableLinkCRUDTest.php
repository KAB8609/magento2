<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Catalog_Product_DownloadableLinkCRUDTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test downloadable link create
     *
     * @magentoDataFixture Api/Mage/Catalog/Product/_fixture/LinkCRUD.php
     * @return void
     */
    public function testDownloadableLinkCreate()
    {
        $tagFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/_data/xml/LinkCRUD.xml');
        $items = Magento_Test_Helper_Api::simpleXmlToArray($tagFixture->items);

        $productId = PHPUnit_Framework_TestCase::getFixture('productData')->getId();

        foreach ($items as $item) {
            foreach ($item as $key => $value) {
                if ($value['type'] == 'file') {
                    $filePath = dirname(__FILE__) . '/_fixture/_data/files/' . $value['file']['filename'];
                    $value['file'] = array(
                        'name' => str_replace('/', '_', $value['file']['filename']),
                        'base64_content' => base64_encode(file_get_contents($filePath)),
                        'type' => $value['type']
                    );
                }
                if ($key == 'link' && $value['sample']['type'] == 'file') {
                    $filePath = dirname(__FILE__) . '/_fixture/_data/files/' . $value['sample']['file']['filename'];
                    $value['sample']['file'] = array(
                        'name' => str_replace('/', '_', $value['sample']['file']['filename']),
                        'base64_content' => base64_encode(file_get_contents($filePath))
                    );
                }

                $resultId = Magento_Test_Helper_Api::call(
                    $this,
                    'catalogProductDownloadableLinkAdd',
                    array(
                        'productId' => $productId,
                        'resource' => $value,
                        'resourceType' => $key
                    )
                );
                $this->assertGreaterThan(0, $resultId);
            }
        }
    }

    /**
     * Test get downloadable link items
     *
     * @return void
     * @magentoDataFixture Api/Mage/Catalog/Product/_fixture/DownloadableWithLinks.php
     */
    public function testDownloadableLinkItems()
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = PHPUnit_Framework_TestCase::getFixture('downloadable');
        $productId = $product->getId();

        $result = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductDownloadableLinkList',
            array('productId' => $productId)
        );
        /** @var Mage_Downloadable_Model_Product_Type $downloadable */
        $downloadable = $product->getTypeInstance();
        $links = $downloadable->getLinks($product);

        $this->assertEquals(count($links), count($result['links']));
        foreach ($result['links'] as $actualLink) {
            foreach ($links as $expectedLink) {
                if ($actualLink['link_id'] == $expectedLink) {
                    $this->assertEquals($expectedLink->getData('title'), $actualLink['title']);
                    $this->assertEquals($expectedLink->getData('price'), $actualLink['price']);
                }
            }
        }
    }

    /**
     * Remove downloadable link
     *
     * @magentoDataFixture Api/Mage/Catalog/Product/_fixture/DownloadableWithLinks.php
     * @return void
     */
    public function testDownloadableLinkRemove()
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = PHPUnit_Framework_TestCase::getFixture('downloadable');
        /** @var Mage_Downloadable_Model_Product_Type $downloadable */
        $downloadable = $product->getTypeInstance();
        $links = $downloadable->getLinks($product);
        foreach ($links as $link) {
            $removeResult = Magento_Test_Helper_Api::call(
                $this,
                'catalogProductDownloadableLinkRemove',
                array(
                    'linkId' => $link->getId(),
                    'resourceType' => 'link'
                )
            );
            $this->assertTrue((bool)$removeResult);
        }
    }
}

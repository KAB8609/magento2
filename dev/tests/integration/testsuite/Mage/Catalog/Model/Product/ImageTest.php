<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Model_Product_ImageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function testSetBaseFilePlaceholder()
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $model = Mage::getModel('Mage_Catalog_Model_Product_Image');
        $model->setDestinationSubdir('image')->setBaseFile('');
        $this->assertEmpty($model->getBaseFile());
        return $model;
    }

    /**
     * @param Mage_Catalog_Model_Product_Image $model
     * @depends testSetBaseFilePlaceholder
     */
    public function testSaveFilePlaceholder($model)
    {
        $processor = $this->getMock('Varien_Image', array('save'));
        $processor->expects($this->exactly(0))->method('save');
        $model->setImageProcessor($processor)->saveFile();
    }

    /**
     * @param Mage_Catalog_Model_Product_Image $model
     * @depends testSetBaseFilePlaceholder
     */
    public function testGetUrlPlaceholder($model)
    {
        $this->assertStringMatchesFormat(
            'http://localhost/pub/media/skin/frontend/%s/Mage_Catalog/images/product/placeholder/image.jpg',
            $model->getUrl()
        );
    }
}

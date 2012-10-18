<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_XmlConnect
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_XmlConnect_Helper_Catalog_ImageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $name
     * @dataProvider getPlaceholderDataProvider
     */
    public function testGetPlaceholder($name)
    {
        $helper = Mage::helper('Mage_XmlConnect_Helper_Catalog_Category_Image');
        $helper->initialize(Mage::getModel('Mage_Catalog_Model_Product'), $name);
        $this->assertFileExists(
            Mage::getDesign()->getSkinFile($helper->getPlaceholder())
        );
    }
    /**
     * @return array
     */
    public function getPlaceholderDataProvider()
    {
        return array(
            array('image'),
            array('small_image'),
            array('thumbnail'),
        );
    }
}

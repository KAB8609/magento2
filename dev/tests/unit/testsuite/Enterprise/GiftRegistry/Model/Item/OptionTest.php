<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftRegistry
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Model_Item_OptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param mixed $product
     * @param mixed $expectedProduct
     * @param int $expectedProductId
     * @dataProvider setProductDataProvider
     */
    public function testSetProduct($product, $expectedProduct, $expectedProductId)
    {
        $model = $this->getMock('Enterprise_GiftRegistry_Model_Item_Option',
            array('getValue'), array(), '', false);
        $model->setProduct($product);

        $this->assertEquals($expectedProduct, $model->getProduct());
        $this->assertEquals($expectedProductId, $model->getProductId());
    }

    public function setProductDataProvider()
    {
        $product = $this->getMock('Mage_Catalog_Model_Product', array('getId'), array(), '', false);
        $product->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(3));
        return array(
            array($product, $product, 3),
            array(null, null, null),
        );
    }
}

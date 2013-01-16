<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Item_BundleFixedTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $helper = $this->getMockBuilder('Saas_PrintedTemplate_Helper_Data')
            ->setMethods(array('__'))
            ->getMock();
        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        $mockItem = $this->getMockBuilder('Mage_Sales_Model_Order_Item')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', 'unsetData'))
            ->getMock();

        $model =  $this->getMockBuilder('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Item_BundleFixed')
            ->setMethods(array('getModel', '_getHelper'))
            ->disableOriginalConstructor()
            ->getMock();
        $model->expects($this->any())
            ->method('_getHelper')
            ->will($this->returnValue($helper));
        $model->expects($this->any())
            ->method('getModel')
            ->will($this->returnValue($mockItem));

        $this->assertEmpty($model->getData());
        $this->assertEmpty($mockItem->getData());

        $reflection = new ReflectionClass(get_class($model));
        $method = $reflection->getMethod('_construct');
        $method->setAccessible(true);
        $method->invokeArgs($model, array());

        $this->assertNotEmpty($model->getData());
        $this->assertNotEmpty($mockItem->getData());
        $this->assertSame($model, $mockItem->getParentItem());
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GoogleShopping\Model;

class AttributeFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Get object manager mock
     *
     * @return \Magento\ObjectManager
     */
    protected function _createObjectManager()
    {
        return $this->getMockBuilder('Magento\ObjectManager')
            ->setMethods(array('create'))
            ->getMockForAbstractClass();
    }

    /**
     * Get helper mock
     *
     * @return \Magento\GoogleShopping\Helper\Data
     */
    protected function _createGsData()
    {
        return $this->getMockBuilder('Magento\GoogleShopping\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
    }

    /**
     * Get default attribute mock
     *
     * @return \Magento\GoogleShopping\Model\Attribute\DefaultAttribute
     */
    protected function _createDefaultAttribute()
    {
        return $this->getMockBuilder('Magento\GoogleShopping\Model\Attribute\DefaultAttribute')
            ->disableOriginalConstructor()
            ->setMethods(array('__wakeup'))
            ->getMock();
    }

    /**
     * @param string $name
     * @param string $expected
     * @dataProvider createAttributeDataProvider
     */
    public function testCreateAttribute($name, $expected)
    {
        $objectManager = $this->_createObjectManager();
        $objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento\GoogleShopping\Model\Attribute\\' . $expected))
            ->will($this->returnValue($this->_createDefaultAttribute()));
        $attributeFactory = new \Magento\GoogleShopping\Model\AttributeFactory(
            $objectManager,
            $this->_createGsData(),
            new \Magento\Stdlib\String
        );
        $attribute = $attributeFactory->createAttribute($name);
        $this->assertEquals($name, $attribute->getName());
    }

    public function createAttributeDataProvider()
    {
        return array(
            array('name', 'Name'),
            array('first_second', 'First_Second'),
            array('first_second_third', 'First_Second_Third')
        );
    }

    /**
     * @param bool $throwException
     * @dataProvider createAttributeDefaultDataProvider
     */
    public function testCreateAttributeDefault($throwException)
    {
        $objectManager = $this->_createObjectManager();
        $objectManager->expects($this->at(0))
            ->method('create')
            ->with($this->equalTo('Magento\GoogleShopping\Model\Attribute\Name'))
            ->will($throwException ? $this->throwException(new \Exception()) : $this->returnValue(false));
        $objectManager->expects($this->at(1))
            ->method('create')
            ->with($this->equalTo('Magento\GoogleShopping\Model\Attribute\DefaultAttribute'))
            ->will($this->returnValue($this->_createDefaultAttribute()));
        $attributeFactory = new \Magento\GoogleShopping\Model\AttributeFactory(
            $objectManager,
            $this->_createGsData(),
            new \Magento\Stdlib\String
        );
        $attribute = $attributeFactory->createAttribute('name');
        $this->assertEquals('name', $attribute->getName());
    }

    public function createAttributeDefaultDataProvider()
    {
        return array(array(true), array(false));
    }

    public function testCreate()
    {
        $objectManager = $this->_createObjectManager();
        $objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\GoogleShopping\Model\Attribute')
            ->will($this->returnValue('some value'));
        $attributeFactory = new \Magento\GoogleShopping\Model\AttributeFactory(
            $objectManager,
            $this->_createGsData(),
            new \Magento\Stdlib\String
        );
        $attribute = $attributeFactory->create();
        $this->assertEquals('some value', $attribute);
    }
}
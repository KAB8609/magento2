<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tax
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Tax_Model_Class_Type_ProductTest extends PHPUnit_Framework_TestCase
{
    public function testGetAssignedObjects()
    {
        $collectionMock = $this->getMockBuilder('Mage_Core_Model_Resource_Db_Collection_Abstract')
            ->setMethods(array(
                'addAttributeToFilter'
            ))
            ->disableOriginalConstructor()
            ->getMock();
        $collectionMock->expects($this->once())
            ->method('addAttributeToFilter')
            ->with($this->equalTo('tax_class_id'), $this->equalTo(1))
            ->will($this->returnSelf());

        $productMock = $this->getMockBuilder('Mage_Catalog_Model_Product')
            ->setMethods(array('getCollection'))
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($collectionMock));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        /** @var $model Mage_Tax_Model_Class_Type_Product */
        $model = $objectManagerHelper->getObject(
            'Mage_Tax_Model_Class_Type_Product',
            array(
                'modelProduct' => $productMock,
                'helper' => $this->getMock('Mage_Tax_Helper_Data', array(), array(), '', false),
                'data' => array('id' => 1)
            )
        );
        $this->assertEquals($collectionMock, $model->getAssignedToObjects());
    }

}
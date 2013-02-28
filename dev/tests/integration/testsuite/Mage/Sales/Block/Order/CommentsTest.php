<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sales_Block_Order_CommentsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Sales_Block_Order_Comments
     */
    protected $_block;

    public function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock('Mage_Sales_Block_Order_Comments');
    }

    /**
     * @param mixed $commentedEntity
     * @param string $expectedClass
     * @dataProvider getCommentsDataProvider
     */
    public function testGetComments($commentedEntity, $expectedClass)
    {
        $this->_block->setEntity($commentedEntity);
        $comments = $this->_block->getComments();
        $this->assertInstanceOf($expectedClass, $comments);
    }

    /**
     * @return array
     */
    public function getCommentsDataProvider()
    {
        return array(
            array(
                Mage::getModel('Mage_Sales_Model_Order_Invoice'),
                'Mage_Sales_Model_Resource_Order_Invoice_Comment_Collection'
            ),
            array(
                Mage::getModel('Mage_Sales_Model_Order_Creditmemo'),
                'Mage_Sales_Model_Resource_Order_Creditmemo_Comment_Collection'
            ),
            array(
                Mage::getModel('Mage_Sales_Model_Order_Shipment'),
                'Mage_Sales_Model_Resource_Order_Shipment_Comment_Collection'
            )
        );
    }

    /**
     * @expectedException Mage_Core_Exception
     */
    public function testGetCommentsWrongEntityException()
    {
        $entity = Mage::getModel('Mage_Catalog_Model_Product');
        $this->_block->setEntity($entity);
        $this->_block->getComments();
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Customer_Block_Account_Dashboard_InfoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Customer_Block_Account_Dashboard_Info
     */
    protected $_block;

    public function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock('Magento_Customer_Block_Account_Dashboard_Info');
    }

    public function testGetSubscriptionObject()
    {
        $object = $this->_block->getSubscriptionObject();
        $this->assertInstanceOf('Magento_Newsletter_Model_Subscriber', $object);

        $object2 = $this->_block->getSubscriptionObject();
        $this->assertSame($object, $object2);
    }
}
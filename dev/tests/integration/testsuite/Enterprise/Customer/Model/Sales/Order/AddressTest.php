<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * magentoDataFixture Enterprise/Customer/_files/order_address_with_attribute.php
 */
class Enterprise_Customer_Model_Sales_Order_AddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Customer_Model_Sales_Order_Address
     */
    protected $_model;

    protected function setUp()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + fixture');

        $this->_model = Mage::getModel('Enterprise_Customer_Model_Sales_Order_Address');
    }

    public function testAttachDataToEntities()
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $address = Mage::getModel('Mage_Sales_Model_Order_Address');
        $address->load('admin@example.com', 'email');

        $entity = new Varien_Object(array('id' => $address->getId()));
        $this->assertEmpty($entity->getData('fixture_address_attribute'));
        $this->_model->attachDataToEntities(array($entity));
        $this->assertEquals('fixture_attribute_custom_value', $entity->getData('fixture_address_attribute'));
    }
}

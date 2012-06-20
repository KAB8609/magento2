<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Abstract
 */
class Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Abstract customer export model
     *
     * @var Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Abstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * Websites array (website id => code)
     *
     * @var array
     */
    protected $_websites = array(
        1 => 'website1',
        2 => 'website2',
    );

    /**
     * Customers array
     *
     * @var array
     */
    protected $_customers = array(
        array(
            'id'         => 1,
            'email'      => 'test1@email.com',
            'website_id' => 1
        ),
        array(
            'id'         => 2,
            'email'      => 'test2@email.com',
            'website_id' => 2
        ),
    );

    public function setUp()
    {
        parent::setUp();

        $this->_model = $this->_getModelMock();
    }

    public function tearDown()
    {
        unset($this->_model);

        parent::tearDown();
    }

    /**
     * Create mock for abstract customer model class
     */
    protected function _getModelMock()
    {
        $customerCollection = new Varien_Data_Collection();
        foreach ($this->_customers as $customer) {
            $customerCollection->addItem(new Varien_Object($customer));
        }

        $modelMock = $this->getMockForAbstractClass('Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Abstract',
            array(), '', false, true, true, array('_getCustomerCollection')
        );
        $property = new ReflectionProperty($modelMock, '_websiteCodeToId');
        $property->setAccessible(true);
        $property->setValue($modelMock, array_flip($this->_websites));

        $modelMock->expects($this->any())
            ->method('_getCustomerCollection')
            ->will($this->returnValue($customerCollection));

        return $modelMock;
    }

    /**
     * Check whether Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Abstract::_customers is filled correctly
     *
     * @covers Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Abstract::_initCustomers()
     */
    public function testInitCustomers()
    {
        $customers = array();
        foreach ($this->_customers as $customer) {
            $email = strtolower($customer['email']);
            if (!isset($this->_customers[$email])) {
                $customers[$email] = array();
            }
            $customers[$email][$customer['website_id']] = $customer['id'];
        }

        $method = new ReflectionMethod($this->_model, '_initCustomers');
        $method->setAccessible(true);
        $method->invoke($this->_model);

        $this->assertAttributeEquals($customers, '_customers', $this->_model);
    }

    /**
     * Check whether Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Abstract::_getCustomerId() returns
     * correct values
     *
     * @depends testInitCustomers
     * @covers Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Abstract::_getCustomerId()
     */
    public function testGetCustomerId()
    {
        $method = new ReflectionMethod($this->_model, '_initCustomers');
        $method->setAccessible(true);
        $method->invoke($this->_model);

        $method = new ReflectionMethod($this->_model, '_getCustomerId');
        $method->setAccessible(true);

        $this->assertEquals($this->_customers[0]['id'],
            $method->invokeArgs($this->_model, array($this->_customers[0]['email'],
                $this->_websites[$this->_customers[0]['website_id']])
            )
        );
        $this->assertEquals($this->_customers[1]['id'],
            $method->invokeArgs($this->_model, array($this->_customers[1]['email'],
                $this->_websites[$this->_customers[1]['website_id']])
            )
        );
        $this->assertFalse(
            $method->invokeArgs($this->_model, array($this->_customers[0]['email'], 'website3'))
        );
        $this->assertFalse(
            $method->invokeArgs($this->_model,
                array('test3@email.com', $this->_websites[$this->_customers[0]['website_id']])
            )
        );
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_WebsiteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Website
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getModel('Magento_Core_Model_Website');
        $this->_model->load(1);
    }

    public function testLoad()
    {
        /* Test loading by id */
        $this->assertEquals(1, $this->_model->getId());
        $this->assertEquals('base', $this->_model->getCode());
        $this->assertEquals('Main Website', $this->_model->getName());

        /* Test loading by code */
        $this->_model->load('admin');
        $this->assertEquals(0, $this->_model->getId());
        $this->assertEquals('admin', $this->_model->getCode());
        $this->assertEquals('Admin', $this->_model->getName());
    }

    /**
     * @covers Magento_Core_Model_Website::setGroups
     * @covers Magento_Core_Model_Website::setStores
     * @covers Magento_Core_Model_Website::getStores
     */
    public function testSetGroupsAndStores()
    {
        /* Groups */
        $expectedGroup = Mage::getModel('Magento_Core_Model_Store_Group');
        $expectedGroup->setId(123);
        $this->_model->setDefaultGroupId($expectedGroup->getId());
        $this->_model->setGroups(array($expectedGroup));

        $groups = $this->_model->getGroups();
        $this->assertSame($expectedGroup, reset($groups));

        /* Stores */
        $expectedStore = Mage::getModel('Magento_Core_Model_Store');
        $expectedStore->setId(456);
        $expectedGroup->setDefaultStoreId($expectedStore->getId());
        $this->_model->setStores(array($expectedStore));

        $stores = $this->_model->getStores();
        $this->assertSame($expectedStore, reset($stores));
    }

    public function testGetGroups()
    {
        $groups = $this->_model->getGroups();
        $this->assertEquals(array(1), array_keys($groups));
        $this->assertInstanceOf('Magento_Core_Model_Store_Group', $groups[1]);
        $this->assertEquals(1, $groups[1]->getId());
    }

    public function testGetGroupIds()
    {
        $this->assertEquals(array(1 => 1), $this->_model->getGroupIds());
    }

    public function testGetGroupsCount()
    {
        $this->assertEquals(1, $this->_model->getGroupsCount());
    }

    public function testGetDefaultGroup()
    {
        $defaultGroup = $this->_model->getDefaultGroup();
        $this->assertInstanceOf('Magento_Core_Model_Store_Group', $defaultGroup);
        $this->assertEquals(1, $defaultGroup->getId());

        $this->_model->setDefaultGroupId(null);
        $this->assertFalse($this->_model->getDefaultGroup());
    }

    public function testGetStores()
    {
        $stores = $this->_model->getStores();
        $this->assertEquals(array(1), array_keys($stores));
        $this->assertInstanceOf('Magento_Core_Model_Store', $stores[1]);
        $this->assertEquals(1, $stores[1]->getId());
    }

    public function testGetStoreIds()
    {
        $this->assertEquals(array(1 => 1), $this->_model->getStoreIds());
    }

    public function testGetStoreCodes()
    {
        $this->assertEquals(array(1 => 'default'), $this->_model->getStoreCodes());
    }

    public function testGetStoresCount()
    {
        $this->assertEquals(1, $this->_model->getStoresCount());
    }

    public function testIsCanDelete()
    {
        $this->assertFalse($this->_model->isCanDelete());
        $this->_model->isReadOnly(true);
        $this->assertFalse($this->_model->isCanDelete());
    }

    public function testGetWebsiteGroupStore()
    {
        $this->assertEquals('1--', $this->_model->getWebsiteGroupStore());
        $this->_model->setGroupId(123);
        $this->_model->setStoreId(456);
        $this->assertEquals('1-123-456', $this->_model->getWebsiteGroupStore());
    }

    public function testGetDefaultGroupId()
    {
        $this->assertEquals(1, $this->_model->getDefaultGroupId());
    }

    public function testGetBaseCurrency()
    {
        $currency = $this->_model->getBaseCurrency();
        $this->assertInstanceOf('Magento_Directory_Model_Currency', $currency);
        $this->assertEquals('USD', $currency->getCode());
    }

    public function testGetDefaultStore()
    {
        $defaultStore = $this->_model->getDefaultStore();
        $this->assertInstanceOf('Magento_Core_Model_Store', $defaultStore);
        $this->assertEquals(1, $defaultStore->getId());
    }

    public function testGetDefaultStoresSelect()
    {
        $this->assertInstanceOf('Magento_DB_Select', $this->_model->getDefaultStoresSelect());
    }

    public function testIsReadonly()
    {
        $this->assertFalse($this->_model->isReadOnly());
        $this->_model->isReadOnly(true);
        $this->assertTrue($this->_model->isReadOnly());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testCRUD()
    {
        $this->_model->setData(
            array(
                'code'              => 'test_website',
                'name'              => 'test website',
                'default_group_id'  => 1,
            )
        );

        /* emulate admin store */
        Mage::app()->getStore()->setId(Magento_Core_Model_AppInterface::ADMIN_STORE_ID);
        $crud = new Magento_Test_Entity($this->_model, array('name' => 'new name'));
        $crud->testCrud();
    }

    public function testCollection()
    {
        $collection = $this->_model->getCollection()
            ->joinGroupAndStore()
            ->addIdFilter(1);
        $this->assertEquals(1, count($collection->getItems()));
    }
}
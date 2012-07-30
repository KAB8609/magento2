<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_User
 */
class Mage_User_Model_RulesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_User_Model_Rules
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_User_Model_Rules;
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCRUD()
    {
        $this->_model->setRoleType('G')
            ->setResourceId(Mage_Backend_Model_Acl_Config::ACL_RESOURCE_ALL)
            ->setPrivileges("")
            ->setAssertId(0)
            ->setRoleId(1)
            ->setPermission('allow');

        $crud = new Magento_Test_Entity($this->_model, array('permission' => 'deny'));
        $crud->testCrud();
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testInitialUserPermissions()
    {
        $adapter = $this->_model->getResource()->getReadConnection();
        $ruleSelect = $adapter->select()
            ->from($this->_model->getResource()->getMainTable());

        $rules = $ruleSelect->query()->fetchAll();
        $this->assertEquals(1, count($rules));
        $this->assertEquals(Mage_Backend_Model_Acl_Config::ACL_RESOURCE_ALL, $rules[0]['resource_id']);
        $this->assertEquals(1, $rules[0]['role_id']);
        $this->assertEquals('allow', $rules[0]['permission']);
    }

    /**
     * @covers Mage_User_Model_Rules::saveRel
     * @magentoDbIsolation enabled
     */
    public function testSetAllowForAllResources()
    {
        $adapter = $this->_model->getResource()->getReadConnection();
        $ruleSelect = $adapter->select()
            ->from($this->_model->getResource()->getMainTable());

        $resources = array('all');

        $this->_model->setRoleId(1)
            ->setResources($resources)
            ->saveRel();

        $rules = $ruleSelect->query()->fetchAll();
        $this->assertEquals(1, count($rules));
        $this->assertEquals(Mage_Backend_Model_Acl_Config::ACL_RESOURCE_ALL, $rules[0]['resource_id']);
        $this->assertEquals(1, $rules[0]['role_id']);
        $this->assertEquals('allow', $rules[0]['permission']);
    }
}


<?php
/**
 * Test class for Mage_Webapi_Model_Authorization_Loader_Role
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Authorization_Loader_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Resource_Acl_Role
     */
    protected $_resourceModelMock;

    /**
     * @var Mage_Webapi_Model_Authorization_Loader_Role
     */
    protected $_model;

    /**
     * @var Mage_Webapi_Model_Authorization_Role_Factory
     */
    protected $_roleFactory;

    /**
     * @var Magento_Acl
     */
    protected $_acl;

    /**
     * Set up before test
     */
    protected function setUp()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);

        $this->_resourceModelMock = $this->getMock('Mage_Webapi_Model_Resource_Acl_Role',
            array('getRolesIds'), array(), '', false);

        $this->_roleFactory = $this->getMock('Mage_Webapi_Model_Authorization_Role_Factory',
            array('createRole'), array(), '', false);

        $this->_acl = $this->getMock('Magento_Acl', array('addRole', 'deny'), array(), '',
            false);

        $this->_model = $helper->getModel('Mage_Webapi_Model_Authorization_Loader_Role', array(
            'roleResource' => $this->_resourceModelMock,
            'roleFactory' => $this->_roleFactory,
        ));
    }

    /**
     * Test for Mage_Webapi_Model_Authorization_Loader_Role::populateAcl
     *
     * Test with existing role Ids
     */
    public function testPopulateAclWithRoles()
    {
        $roleOne = new Mage_Webapi_Model_Authorization_Role(3);
        $roleTwo = new Mage_Webapi_Model_Authorization_Role(4);
        $roleIds = array(3, 4);
        $createRoleMap = array(
            array(array(3), $roleOne),
            array(array(4), $roleTwo),
        );
        $this->_resourceModelMock->expects($this->once())
            ->method('getRolesIds')
            ->will($this->returnValue($roleIds));

        $this->_roleFactory->expects($this->exactly(count($roleIds)))
            ->method('createRole')
            ->will($this->returnValueMap($createRoleMap));

        $this->_acl->expects($this->exactly(count($roleIds)))
            ->method('addRole')
            ->with($this->logicalOr($roleOne, $roleTwo));

        $this->_acl->expects($this->exactly(count($roleIds)))
            ->method('deny')
            ->with($this->logicalOr($roleOne, $roleTwo));

        $this->_model->populateAcl($this->_acl);
    }

    /**
     * Test for Mage_Webapi_Model_Authorization_Loader_Role::populateAcl
     *
     * Test with No existing role Ids
     */
    public function testPopulateAclWithNoRoles()
    {
        $this->_resourceModelMock->expects($this->once())
            ->method('getRolesIds')
            ->will($this->returnValue(array()));

        $this->_roleFactory->expects($this->never())
            ->method('createRole');

        $this->_acl->expects($this->never())
            ->method('addRole');

        $this->_acl->expects($this->never())
            ->method('deny');

        $this->_model->populateAcl($this->_acl);
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Authorization_Policy_AclTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Authorization_Policy_Acl
     */
    protected $_model;

    protected $_aclMock;

    public function setUp()
    {
        $this->_aclMock = $this->getMock('Magento_Acl');
        $this->_model = new Magento_Authorization_Policy_Acl($this->_aclMock);
    }

    public function testIsAllowedReturnsTrueIfResourceIsAllowedToRole()
    {
        $this->_aclMock->expects($this->once())
            ->method('isAllowed')
            ->with('some_role', 'some_resource')
            ->will($this->returnValue(true));

        $this->assertTrue($this->_model->isAllowed('some_role', 'some_resource'));
    }

    public function testIsAllowedReturnsFalseIfRoleDoesntExist()
    {
        $this->_aclMock->expects($this->once())
            ->method('isAllowed')
            ->with('some_role', 'some_resource')
            ->will($this->throwException(new Zend_Acl_Role_Registry_Exception));

        $this->_aclMock->expects($this->once())
            ->method('has')
            ->with('some_resource')
            ->will($this->returnValue(true));

        $this->assertFalse($this->_model->isAllowed('some_role', 'some_resource'));
    }

    public function testIsAllowedReturnsTrueIfResourceDoesntExistAndAllResourcesAreNotPermitted()
    {
        $this->_aclMock->expects($this->at(0))
            ->method('isAllowed')
            ->with('some_role', 'some_resource')
            ->will($this->throwException(new Zend_Acl_Role_Registry_Exception));

        $this->_aclMock->expects($this->once())
            ->method('has')
            ->with('some_resource')
            ->will($this->returnValue(false));

        $this->_aclMock->expects($this->at(2))
            ->method('isAllowed')
            ->with('some_role', null)
            ->will($this->returnValue(true));

        $this->assertTrue($this->_model->isAllowed('some_role', 'some_resource'));
    }
}

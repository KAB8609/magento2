<?php
/**
 * Test class for Mage_Webapi_Model_Acl_User_RoleUpdater
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Acl_User_RoleUpdaterrTest extends PHPUnit_Framework_TestCase
{
    public function testUpdate()
    {
        $userId = 5;
        $expectedRoleId = 3;

        $helper = new Magento_Test_Helper_ObjectManager($this);

        $request = $this->getMockBuilder('Mage_Core_Controller_Request_Http')
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->any())->method('getParam')->will($this->returnValueMap(array(
            array('user_id', null, $userId)
        )));

        $userModel = $this->getMockBuilder('Mage_Webapi_Model_Acl_User')
            ->setMethods(array('getRoleId', 'load'))
            ->disableOriginalConstructor()
            ->getMock();
        $userModel->expects($this->once())->method('load')
            ->with($userId, null)->will($this->returnSelf());
        $userModel->expects($this->once())->method('getRoleId')
            ->with()->will($this->returnValue($expectedRoleId));

        $userFactory = $this->getMockBuilder('Mage_Webapi_Model_Acl_User_Factory')
            ->setMethods(array('create'))
            ->disableOriginalConstructor()
            ->getMock();
        $userFactory->expects($this->once())->method('create')
            ->with(array())->will($this->returnValue($userModel));

        /** @var Mage_Webapi_Model_Acl_Role_InRoleUserUpdater $model */
        $model = $helper->getModel('Mage_Webapi_Model_Acl_User_RoleUpdater', array(
            'request' => $request,
            'userFactory' => $userFactory
        ));

        $this->assertEquals($expectedRoleId, $model->update(array()));
    }
}

<?php
/**
 * Magento_Webhook_Model_User
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_UserTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Webhook_Model_User */
    protected $_user;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockAclUser;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_mockAuthorization;

    public function setUp()
    {
        $webApiId = 'web api id';

        $this->_mockAclUser = $this->getMockBuilder('Magento_Webapi_Model_Acl_User_Factory')
            ->setMethods(array('load', 'getRoleId', 'getSecret'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockUserFactory = $this->getMockBuilder('Magento_Webapi_Model_Acl_User_Factory')
            ->setMethods(array('create'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockUserFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_mockAclUser));

        $this->_mockAclUser->expects($this->once())
            ->method('load')
            ->with($this->equalTo($webApiId));

        $mockRLocatorFactory = $this->getMockBuilder('Magento_Webapi_Model_Authorization_Role_Locator_Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockAclUser->expects($this->once())
            ->method('getRoleId')
            ->will($this->returnValue('role_id'));

        $mockRLocatorFactory->expects($this->once())
            ->method('create')
            ->with(array('data' => array('roleId' => 'role_id')))
            ->will($this->returnValue('role_locator'));

        $this->_mockAuthorization = $this->getMockBuilder('Magento_Authorization')
            ->setMethods(array('isAllowed'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockAclPolicy = $this->getMockBuilder('Magento_Webapi_Model_Authorization_Policy_Acl')
            ->disableOriginalConstructor()
            ->getMock();

        $mockAuthFactory = $this->getMockBuilder('Magento_Authorization_Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $mockAuthFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_mockAuthorization));

        $this->_user = new Magento_Webhook_Model_User(
            $mockUserFactory,
            $mockRLocatorFactory,
            $mockAclPolicy,
            $mockAuthFactory,
            $webApiId
        );
    }

    public function testGetSharedSecret()
    {
        $sharedSecret = 'some random shared secret';

        $this->_mockAclUser->expects($this->once())
            ->method('getSecret')
            ->will($this->returnValue($sharedSecret));

        $this->assertSame($sharedSecret, $this->_user->getSharedSecret());
    }

    public function testHasPermission()
    {
        $allowedTopic = 'allowed topic';
        $notAllowedTopic = 'not allowed topic';

        $this->_mockAuthorization->expects($this->any())
            ->method('isAllowed')
            ->will(
                $this->returnValueMap(
                    array(
                         array($allowedTopic, null, true),
                         array($notAllowedTopic, null, false)
                    )
                )
            );

        $this->assertTrue($this->_user->hasPermission($allowedTopic));
        $this->assertFalse($this->_user->hasPermission($notAllowedTopic));
    }
}

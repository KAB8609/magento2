<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\WebsiteRestriction\Model\Plugin;

class CustomerRegistrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\WebsiteRestriction\Model\Plugin\CustomerRegistration
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_restrictionConfig;

    protected function setUp()
    {
        $this->_restrictionConfig = $this->getMock('Magento\WebsiteRestriction\Model\ConfigInterface');
        $this->_model = new \Magento\WebsiteRestriction\Model\Plugin\CustomerRegistration($this->_restrictionConfig);
    }

    public function testAfterIsRegistrationIsAllowedRestrictsRegistrationIfRestrictionModeForbidsIt()
    {
        $storeMock = $this->getMock('Magento\Core\Model\Store', array(), array(), '', false);
        $storeMock->expects($this->any())
            ->method('isAdmin')
            ->will($this->returnValue(false));
        $this->_restrictionConfig->expects($this->any())
            ->method('isRestrictionEnabled')
            ->will($this->returnValue(true));
        $this->_restrictionConfig->expects($this->once())
            ->method('getMode')->will($this->returnValue(\Magento\WebsiteRestriction\Model\Mode::ALLOW_NONE));
        $this->assertFalse($this->_model->afterIsRegistrationAllowed(true));
    }
}
<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\HTTP\PhpEnvironment;

use \Magento\TestFramework;

class ServerAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\HTTP\PhpEnvironment\ServerAddress
     */
    protected $_serverAddress;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\App\Request\Http
     */
    protected $_request;

    protected function setUp()
    {
        $this->_request = $this->getMockBuilder('Magento\App\Request\Http')
            ->disableOriginalConstructor()
            ->setMethods(array('getServer'))
            ->getMock();

        $objectManager = new TestFramework\Helper\ObjectManager($this);
        $this->_serverAddress = $objectManager->getObject('Magento\HTTP\PhpEnvironment\ServerAddress', array(
            'httpRequest' => $this->_request
        ));
    }

    /**
     * @dataProvider getServerAddressProvider
     */
    public function testGetServerAddress($serverVar, $expected, $ipToLong)
    {
        $this->_request->expects($this->atLeastOnce())
            ->method('getServer')
            ->with('SERVER_ADDR')
            ->will($this->returnValue($serverVar));
        $this->assertEquals($expected, $this->_serverAddress->getServerAddress($ipToLong));
    }

    /**
     * @return array
     */
    public function getServerAddressProvider()
    {
        return array(
            array(null, false, false),
            array('192.168.0.1', '192.168.0.1', false),
            array('192.168.1.1', ip2long('192.168.1.1'), true)
        );
    }
}

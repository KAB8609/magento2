<?php
/**
 * Test services for name collisions.
 *
 * Let we have two service interfaces called Foo\Bar\Service\SomeBazV1Interface and Foo\Bar\Service\Some\BazV1Interface.
 * Given current name generation logic both are going to be translated to BarSomeBazV1. This test checks such things
 * are not going to happen.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi;

class ServiceNameCollisionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test there are no collisions between service names.
     *
     * @see \Magento\Webapi\Model\Soap\Config::getServiceName()
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testServiceNameCollisions()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Webapi\Model\Soap\Config $soapConfig */
        $soapConfig = $objectManager->get('Magento\Webapi\Model\Soap\Config');
        /** @var \Magento\Webapi\Model\Config $webapiConfig */
        $webapiConfig = $objectManager->get('Magento\Webapi\Model\Config');
        $serviceNames = array();

        foreach ($webapiConfig->getServices() as $serviceClassName => $serviceData) {
            $newServiceName = $soapConfig->getServiceName($serviceClassName);
            $this->assertFalse(in_array($newServiceName, $serviceNames));
            $serviceNames[] = $newServiceName;
        }
    }
}
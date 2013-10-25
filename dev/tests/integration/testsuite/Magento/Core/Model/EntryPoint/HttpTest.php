<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\EntryPoint;

class HttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testProcessRequestBootstrapException()
    {
        if (!\Magento\TestFramework\Helper\Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Can\'t test entry point response without sending headers');
        }

        $objectManager = $this->getMock('Magento\ObjectManager', array('get', 'create', 'configure'));
        $objectManager->expects($this->any())
            ->method('get')
            ->with('Magento\Error\HandlerInterface')
            ->will($this->returnValue(new \Magento\Error\Handler()));

        $objectManager->expects($this->any())
            ->method('get')
            ->will($this->throwException(new \Magento\BootstrapException('exception_message')));

        $config = $this->getMock('Magento\Core\Model\Config\Primary', array(), array(), '', false);


        /** @var \Magento\Core\Model\EntryPoint\Http $model */
        $model = new \Magento\Core\Model\EntryPoint\Http($config, $objectManager);
        ob_start();
        $model->processRequest();
        $content = ob_get_clean();

        $headers = xdebug_get_headers();
        $this->assertContains('Content-Type: text/plain', $headers);
        $this->assertEquals('exception_message', $content, 'The response must contain exception message, and only it');
    }
}

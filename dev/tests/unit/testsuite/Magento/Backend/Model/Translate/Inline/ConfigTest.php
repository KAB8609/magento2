<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Translate\Inline;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testIsActive()
    {
        $result = 'result';
        $backendConfig = $this->getMockForAbstractClass('Magento\Backend\App\ConfigInterface');
        $backendConfig
            ->expects($this->once())
            ->method('getFlag')
            ->with($this->equalTo('dev/translate_inline/active_admin'))
            ->will($this->returnValue($result));
        $config = new Config($backendConfig);
        $this->assertEquals($result, $config->isActive('any'));
    }
}
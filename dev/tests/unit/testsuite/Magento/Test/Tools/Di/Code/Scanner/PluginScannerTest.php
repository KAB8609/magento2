<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Test\Tools\Di\Code\Scanner;

class PluginScannerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->_model = new \Magento\Tools\Di\Code\Scanner\PluginScanner();
        $this->_testDir = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../../') . '/_files');
        $this->_testFiles = array(
            $this->_testDir . '/app/code/Magento/SomeModule/etc/di.xml',
            $this->_testDir . '/app/etc/di/config.xml',
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testCollectEntities()
    {
        $actual = $this->_model->collectEntities($this->_testFiles);
        $expected = array(
            'Magento_Core_Model_Cache_TagPlugin',
            'Magento_Core_Model_Action_Plugin',
            'Custom_PageCache_Model_Action_Plugin',
        );
        $this->assertEquals($expected, $actual);
    }
}

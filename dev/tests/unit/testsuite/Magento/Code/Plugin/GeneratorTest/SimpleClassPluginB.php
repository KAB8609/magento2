<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Code\Plugin\GeneratorTest;

class SimpleClassPluginB
{
    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return string
     */
    public function aroundDoWork(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        return '<PluginB>' . $invocationChain->proceed($arguments) . '</PluginB>';
    }
}
<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Customer\Block\Widget\Dob
 */
class Magento_Customer_Block_Widget_DobTest extends PHPUnit_Framework_TestCase
{
    public function testGetDateFormat()
    {
        $block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Customer\Block\Widget\Dob');
        $this->assertNotEmpty($block->getDateFormat());
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Customer_OnlineTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetFilterFormHtml()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        $layout = new Mage_Core_Model_Layout(array('area' => 'adminhtml'));
        $block = $layout->createBlock('Mage_Adminhtml_Block_Customer_Online', 'block');
        $this->assertNotEmpty($block->getFilterFormHtml());
    }
}

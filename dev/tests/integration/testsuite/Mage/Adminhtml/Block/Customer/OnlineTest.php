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
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('Mage_Core_Model_Layout', array('area' => Mage_Core_Model_App_Area::AREA_ADMINHTML));
        /** @var $block Mage_Adminhtml_Block_Customer_Online */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Customer_Online', 'block');
        $this->assertNotEmpty($block->getFilterFormHtml());
    }
}

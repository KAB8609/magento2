<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Magento_Connect_Controller_Adminhtml_Extension_Local
 *
 * @magentoAppArea adminhtml
 */
class Magento_Connect_Controller_Adminhtml_Extension_LocalTest extends Magento_Backend_Utility_Controller
{
    public function testIndexAction()
    {
        $expected = '?return=' . urlencode(Mage::helper('Magento_Backend_Helper_Data')->getHomePageUrl());
        $this->dispatch('backend/admin/extension_local/index');
        $this->assertRedirect($this->stringEndsWith($expected));
    }
}
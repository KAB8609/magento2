<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_GoogleShopping
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_GoogleShopping_Controller_Adminhtml_GoogleShopping_ItemsTest extends Mage_Backend_Utility_Controller
{
    public function testIndexAction()
    {
        $this->dispatch('backend/admin/googleshopping_items/index/store/1/');
        $body = $this->getResponse()->getBody();
        $this->assertSelectCount('div#items', 1, $body);
        $this->assertSelectCount('div#googleshopping_selection_search_grid_', 1, $body);
    }
}
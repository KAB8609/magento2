<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_CatalogEvent
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Enterprise_CatalogEvent_Controller_Adminhtml_Catalog_EventTest extends Magento_Backend_Utility_Controller
{
    public function testEditActionSingleStore()
    {
        $this->dispatch('backend/admin/catalog_event/new');
        $body = $this->getResponse()->getBody();
        $this->assertNotContains('name="store_switcher"', $body);
    }

    /**
     * @magentoDataFixture Magento/Core/_files/store.php
     * @magentoDataFixture Enterprise/CatalogEvent/_files/events.php
     */
    public function testEditActionMultipleStore()
    {
        /** @var $event Enterprise_CatalogEvent_Model_Event */
        $event = Mage::getModel('Enterprise_CatalogEvent_Model_Event');
        $event->load(Enterprise_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE, 'display_state');
        $this->dispatch('backend/admin/catalog_event/edit/id/' . $event->getId());
        $body = $this->getResponse()->getBody();

        $this->assertContains('name="store_switcher"', $body);
        $event->delete();
        unset($event);
    }
}

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

class Enterprise_CatalogEvent_CategoryControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * Covers Enterprise_CatalogEvent_Block_Adminhtml_Catalog_Category_Edit_Buttons::addButtons for Add Event button
     *
     * @magentoDataFixture Mage/Catalog/_files/categories.php
     */
    public function testEditCategoryAction()
    {
        $this->dispatch('admin/catalog_category/edit/id/3');
        $this->assertContains(
            'onclick="setLocation(\'http://localhost/index.php/admin/catalog_event/new/category_id/',
            $this->getResponse()->getBody()
        );
    }

    /**
     * Covers Enterprise_CatalogEvent_Block_Adminhtml_Catalog_Category_Edit_Buttons::addButtons for Edit Event button
     *
     * @magentoDataFixture Mage/Catalog/_files/categories.php
     * @magentoDataFixture eventDataFixture
     */
    public function testEditCategoryActionEditEvent()
    {
        $this->dispatch('admin/catalog_category/edit/id/3');
        $this->assertContains(
            'onclick="setLocation(\'http://localhost/index.php/admin/catalog_event/edit/id/',
            $this->getResponse()->getBody()
        );
    }

    public static function eventDataFixture()
    {
        $event = new Enterprise_CatalogEvent_Model_Event;
        $event->setStoreId(0);
        $event->setCategoryId('3');
        $event->setStoreDateStart(date('Y-m-d H:i:s'))->setStoreDateEnd(date('Y-m-d H:i:s', time() + 3600));
        $event->save();
    }
}

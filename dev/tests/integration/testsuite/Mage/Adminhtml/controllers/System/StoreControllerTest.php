<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_System_StoreControllerTest extends Mage_Backend_Utility_Controller
{
    public function testIndexAction()
    {
        $this->dispatch('backend/admin/system_store/index');
        $this->assertContains('Create Store View', $this->getResponse()->getBody());
    }

    /**
     * @magentoConfigFixture global/functional_limitation/max_store_count 1
     */
    public function testIndexActionRestricted()
    {
        $this->dispatch('backend/admin/system_store/index');
        $response = $this->getResponse()->getBody();
        $this->assertNotContains('Create Store View', $response);
        $this->assertContains('You are using the maximum number of store views allowed.', $response);
    }
}
<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_WebsiteRestriction
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_WebsiteRestriction_Controller_IndexTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoConfigFixture current_store general/restriction/is_active 1
     * @magentoConfigFixture current_store general/restriction/mode 0
     * @magentoConfigFixture current_store general/restriction/cms_page page_design_blank
     * @magentoConfigFixture current_store general/restriction/http_status 1
     * @magentoDataFixture Mage/Cms/_files/pages.php
     */
    public function testStubAction()
    {
        $page = Mage::getModel('Mage_Cms_Model_Page');
        $page->load('page100', 'identifier'); // fixture

        $websiteId = Mage::app()->getWebsite('base')->getId(); // fixture, pre-installed
        /**
         * besides more expensive, cleaning by tags currently triggers system setup = DDL = breaks transaction
         * therefore cleanup is performed by cache ID
         */
        Mage::app()->removeCache("RESTRICTION_LANGING_PAGE_{$websiteId}");
        $this->markTestIncomplete('MAGETWO-4342');

        $this->dispatch('restriction/index/stub');
        $body = $this->getResponse()->getBody();
        $this->assertContains('<h1>Cms Page Design Blank Title</h1>', $body);
        $this->assertContains('theme/frontend/default/blank/en_US/Mage_Page/favicon.ico', $body);
        $this->assertHeaderPcre('Http/1.1', '/^503 Service Unavailable$/');
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CatalogSearch
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_CatalogSearch
 */
class Mage_CatalogSearch_AjaxControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Mage/CatalogSearch/_files/query.php
     */
    public function testSuggestAction()
    {
        $this->getRequest()->setParam('q', 'query_text');
        $this->dispatch('catalogsearch/ajax/suggest');
        $this->assertContains('query_text', $this->getResponse()->getBody());
    }
}

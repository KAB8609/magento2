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

class Mage_CatalogSearch_ResultControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Mage/CatalogSearch/_files/query.php
     * @magentoConfigFixture current_store general/locale/code de_DE
     */
    public function testIndexActionTranslation()
    {
        $this->getRequest()->setParam('q', 'query_text');
        $this->dispatch('catalogsearch/result');

        $fixtureTranslate = __('Fixture string');
        $this->assertSame('Fixture translation', (string)$fixtureTranslate);

        $responseBody = $this->getResponse()->getBody();

        $this->assertNotContains('Search:', $responseBody);
        $this->assertStringMatchesFormat('%aSuche%s:%a', $responseBody);

        $this->assertNotContains('Search entire store here...', $responseBody);
        $this->assertContains('Den gesamten Shop durchsuchen...', $responseBody);
    }
}

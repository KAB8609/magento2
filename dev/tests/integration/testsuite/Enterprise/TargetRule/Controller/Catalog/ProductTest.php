<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_TargetRule
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_TargetRule_Controller_Catalog_ProductTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * Covers Enterprise/TargetRule/view/frontend/catalog/product/list/related.html
     * Checks if related products are displayed
     *
     * @magentoDataFixture Mage/Catalog/controllers/_files/products.php
     */
    public function testProductViewActionRelated()
    {
        /*
         * add @magentoDataFixture Enterprise/TargetRule/_files/related.php after fix
         */
        $this->markTestIncomplete('Bug MAGE-6555');
        $this->dispatch('catalog/product/view/id/1');
        $content = $this->getResponse()->getBody();
        $this->assertContains('<div class="box-collateral box-related">', $content);
        $this->assertContains('Simple Product 2 Name', $content);
    }

    /**
     * Covers Enterprise/TargetRule/view/frontend/catalog/product/list/upsell.html
     * Checks if up-sell products are displayed
     *
     * @magentoDataFixture Mage/Catalog/controllers/_files/products.php
     */
    public function testProductViewActionUpsell()
    {
        /*
         * add @magentoDataFixture Enterprise/TargetRule/_files/upsell.php after fix
         */
        $this->markTestIncomplete('Bug MAGE-6555');

        $this->dispatch('catalog/product/view/id/1');
        $content = $this->getResponse()->getBody();
        $this->assertContains('<div class="box-collateral box-up-sell">', $content);
        $this->assertContains('Simple Product 2 Name', $content);
    }
}

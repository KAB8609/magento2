<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Controller_Catalog_Product_ReviewTest extends Mage_Backend_Utility_Controller
{
    /**
     * @magentoDataFixture Mage/Review/_files/review_xss.php
     */
    public function testEditActionProductNameXss()
    {
        $this->dispatch('backend/admin/catalog_product_review/edit/id/1');
        $responseBody = $this->getResponse()->getBody();
        $this->assertContains('&lt;script&gt;alert(&quot;xss&quot;);&lt;/script&gt;', $responseBody);
        $this->assertNotContains('<script>alert("xss");</script>', $responseBody);
    }
}
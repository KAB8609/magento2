<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     theme
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Mage/Catalog/controllers/_files/products.php
 */
class Theme_Enterprise_Default_ControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * Assert that a page content contains references to both jQuery and jQzoom JavaScript libraries
     *
     * @param string $content
     */
    protected function _assertContainsJqZoom($content)
    {
        $this->assertContains('http://localhost/pub/lib/jquery/jquery-1.7.1.min.js', $content);
        $this->assertContains('/js/jqzoom/js/jquery.jqzoom-core-pack.js', $content);
        $this->assertContains('/js/jqzoom/css/jquery.jqzoom.css', $content);
    }

    /**
     * @magentoConfigFixture current_store design/theme/full_name enterprise/fixed
     */
    public function testCatalogProductView()
    {
        $this->dispatch('catalog/product/view/id/1');
        $this->_assertContainsJqZoom($this->getResponse()->getBody());
    }

    /**
     * @magentoConfigFixture current_store design/theme/full_name enterprise/fixed
     */
    public function testReviewProductList()
    {
        $this->dispatch('review/product/list/id/1');
        $this->_assertContainsJqZoom($this->getResponse()->getBody());
    }
}

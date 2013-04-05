<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Mage_Catalog_ProductControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     */
    public function testViewAction()
    {
        $this->getRequest()->setParam('options', Enterprise_GiftRegistry_Block_Product_View::FLAG);
        $this->dispatch('catalog/product/view/id/1');
        $body = $this->getResponse()->getBody();
        $this->assertContains('<span>Add to Gift Registry</span>', $body);
        $this->assertContains('http://localhost/index.php/giftregistry/index/cart/', $body);
    }
}

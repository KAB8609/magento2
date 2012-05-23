<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Checkout_LayoutTest extends PHPUnit_Framework_TestCase
{
    public function testCartLayout()
    {
        Mage::getDesign()->setDesignTheme('enterprise/default/default');
        $layout = new Mage_Core_Model_Layout();
        $layout->getUpdate()->addHandle('checkout_cart_index');
        $layout->getUpdate()->load();
        $this->assertNotEmpty($layout->getUpdate()->asSimplexml()->xpath('//block[@name="sku.failed.products"]'));
    }
}

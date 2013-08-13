<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Customer_Edit_Tab_View_AccordionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Adminhtml_Block_Customer_Edit_Tab_View_Accordion
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $customer->load(1);
        Mage::register('current_customer', $customer);
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getModel(
            'Magento_Core_Model_Layout',
            array('area' => Magento_Core_Model_App_Area::AREA_ADMINHTML)
        );
        $this->_block = $layout->createBlock('Magento_Adminhtml_Block_Customer_Edit_Tab_View_Accordion');
    }

    /**
     * magentoDataFixture Mage/Customer/_files/customer.php
     */
    public function testToHtml()
    {
        $this->assertContains('Wishlist - 0 item(s)', $this->_block->toHtml());
    }
}

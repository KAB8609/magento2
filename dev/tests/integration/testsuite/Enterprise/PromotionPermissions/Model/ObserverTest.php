<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_PromotionPermissions
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_PromotionPermissions_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Core_Model_Layout */
    protected $_layout = null;

    protected function setUp()
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        Mage::getConfig()->setCurrentAreaCode(Mage::helper("Mage_Backend_Helper_Data")->getAreaCode());
        $this->_layout = new Mage_Core_Model_Layout;
    }

    protected function tearDown()
    {
        $this->_layout = null;
    }

    /**
     * @dataProvider blockHtmlBeforeDataProvider
     */
    public function testAdminhtmlBlockHtmlBefore($parentBlock, $childBlock)
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        $block = $this->_layout->createBlock('Mage_Adminhtml_Block_Template', $parentBlock);
        $this->_layout->addBlock('Mage_Adminhtml_Block_Template', $childBlock, $parentBlock);
        $gridBlock = $this->_layout->addBlock('Mage_Adminhtml_Block_Template', 'banners_grid_serializer', $childBlock);

        $this->assertSame(
            $gridBlock,
            $this->_layout->getChildBlock($childBlock, 'banners_grid_serializer')
        );
        Mage::getConfig()->setNode('modules/Enterprise_Banner/active', '1');
        $event = new Varien_Event_Observer();
        $event->setBlock($block);
        $observer = new Enterprise_PromotionPermissions_Model_Observer;
        $observer->adminhtmlBlockHtmlBefore($event);

        $this->assertFalse($this->_layout->getChildBlock($childBlock, 'banners_grid_serializer'));
    }

    public function blockHtmlBeforeDataProvider()
    {
        return array(
            array('promo_quote_edit_tabs', 'salesrule.related.banners'),
            array('promo_catalog_edit_tabs', 'catalogrule.related.banners'),
        );
    }
}

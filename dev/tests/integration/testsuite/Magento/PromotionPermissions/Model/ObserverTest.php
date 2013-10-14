<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PromotionPermissions
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PromotionPermissions\Model;

/**
 * @magentoAppArea adminhtml
 */
class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout = null;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleListMock;

    protected function setUp()
    {
        $this->_moduleListMock = $this->getMock('Magento\App\ModuleListInterface');
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->addSharedInstance($this->_moduleListMock, 'Magento\App\ModuleList');
        $objectManager->get('Magento\Config\ScopeInterface')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        $this->_layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout');
    }

    /**
     * @dataProvider blockHtmlBeforeDataProvider
     * @magentoAppIsolation enabled
     */
    public function testAdminhtmlBlockHtmlBefore($parentBlock, $childBlock)
    {
        $block = $this->_layout->createBlock('Magento\Adminhtml\Block\Template', $parentBlock);
        $this->_layout->addBlock('Magento\Adminhtml\Block\Template', $childBlock, $parentBlock);
        $gridBlock = $this->_layout->addBlock(
            'Magento\Adminhtml\Block\Template',
            'banners_grid_serializer',
            $childBlock
        );

        $this->assertSame(
            $gridBlock,
            $this->_layout->getChildBlock($childBlock, 'banners_grid_serializer')
        );
        $this->_moduleListMock->expects($this->any())->method('getModule')->with('Magento_Banner')
            ->will($this->returnValue(true));
        $event = new \Magento\Event\Observer();
        $event->setBlock($block);
        $observer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\PromotionPermissions\Model\Observer');
        $observer->adminhtmlBlockHtmlBefore($event);

        $this->assertFalse($this->_layout->getChildBlock($childBlock, 'banners_grid_serializer'));
    }

    /**
     * @return array
     */
    public function blockHtmlBeforeDataProvider()
    {
        return array(
            array('promo_quote_edit_tabs', 'salesrule.related.banners'),
            array('promo_catalog_edit_tabs', 'catalogrule.related.banners'),
        );
    }
}

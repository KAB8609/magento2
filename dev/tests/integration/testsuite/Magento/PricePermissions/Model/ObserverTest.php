<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PricePermissions
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PricePermissions\Model;

/**
 * @magentoAppArea adminhtml
 */
class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\View\LayoutInterface */
    protected $_layout = null;

    protected function setUp()
    {
        parent::setUp();
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Config\ScopeInterface')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        $this->_layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\View\LayoutInterface');
    }

    public function testAdminhtmlBlockHtmlBeforeProductOpt()
    {
        $parentBlock = $this->_layout->createBlock('Magento\Adminhtml\Block\Template', 'admin.product.options');
        $optionsBlock = $this->_layout->addBlock(
            'Magento\Adminhtml\Block\Template',
            'options_box',
            'admin.product.options'
        );

        $this->_initSession();
        $this->_runAdminhtmlBlockHtmlBefore($parentBlock);

        $this->assertFalse($optionsBlock->getCanEditPrice());
        $this->assertFalse($optionsBlock->getCanReadPrice());
    }

    public function testAdminhtmlBlockHtmlBeforeBundleOpt()
    {
        $parentBlock = $this->_layout->createBlock(
            'Magento\Adminhtml\Block\Template',
            'adminhtml.catalog.product.edit.tab.bundle.option'
        );
        $selectionBlock = $this->_layout->addBlock(
            'Magento\Adminhtml\Block\Template',
            'selection_template',
            'adminhtml.catalog.product.edit.tab.bundle.option'
        );

        $this->_initSession();
        $this->_runAdminhtmlBlockHtmlBefore($parentBlock);

        $this->assertFalse($parentBlock->getCanReadPrice());
        $this->assertFalse($selectionBlock->getCanReadPrice());
        $this->assertFalse($parentBlock->getCanEditPrice());
        $this->assertFalse($selectionBlock->getCanEditPrice());
    }

    /**
     * Prepare event and run \Magento\PricePermissions\Model\Observer::adminhtmlBlockHtmlBefore
     *
     * @param \Magento\Core\Block\AbstractBlock $block
     */
    protected function _runAdminhtmlBlockHtmlBefore(\Magento\Core\Block\AbstractBlock $block)
    {
        $event = new \Magento\Event\Observer();
        $event->setBlock($block);
        $observer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\PricePermissions\Model\Observer');
        $observer->adminControllerPredispatch($event);
        $observer->adminhtmlBlockHtmlBefore($event);
    }

    /**
     * Prepare session
     */
    protected function _initSession()
    {
        $user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\User\Model\User');
        $user->setId(2)->setRole(true);
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Backend\Model\Auth\Session');
        $session->setUpdatedAt(time())->setUser($user);
    }
}

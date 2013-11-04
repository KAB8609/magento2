<?php
/**
 * Test for \Magento\Webapi\Block\Adminhtml\User\Edit\Tabs block.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Block\Adminhtml\User\Edit;

/**
 * @magentoAppArea adminhtml
 */
class TabsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Webapi\Block\Adminhtml\User\Edit\Tabs
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();

        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_layout = $this->_objectManager->get('Magento\View\LayoutInterface');
        $this->_block = $this->_layout->createBlock('Magento\Webapi\Block\Adminhtml\User\Edit\Tabs',
            'webapi.user.edit.tabs');
    }

    protected function tearDown()
    {
        $this->_objectManager->removeSharedInstance('Magento\View\LayoutInterface');
        unset($this->_objectManager, $this->_layout, $this->_block);
    }

    /**
     * Test _beforeToHtml method.
     */
    public function testBeforeToHtml()
    {
        // TODO: Move to unit tests after MAGETWO-4015 complete.
        /** @var \Magento\Webapi\Block\Adminhtml\User\Edit\Tab\Main $mainTabBlock */
        $mainTabBlock = $this->_layout->addBlock(
            'Magento\Core\Block\Text',
            'webapi.user.edit.tab.main',
            'webapi.user.edit.tabs'
        )->setText('Main Block Content');

        $this->_layout->addBlock(
            'Magento\Core\Block\Text',
            'webapi.user.edit.tab.roles.grid',
            'webapi.user.edit.tabs'
        )->setText('Grid Block Content');

        $apiUser = new \Magento\Object(array(
            'role_id' => 1
        ));
        $this->_block->setApiUser($apiUser);
        $this->_block->toHtml();

        $this->assertSame($apiUser, $mainTabBlock->getApiUser());

        $tabs = $this->_getProtectedTabsValue($this->_block);
        $this->assertArrayHasKey('main_section', $tabs);
        $this->assertInstanceOf('Magento\Object', $tabs['main_section']);
        $this->assertEquals(array(
            'label' => 'User Info',
            'title' => 'User Info',
            'content' => 'Main Block Content',
            'active' => '1',
            'url' => '#',
            'id' => 'main_section',
            'tab_id' => 'main_section',
        ), $tabs['main_section']->getData());

        $this->assertArrayHasKey('roles_section', $tabs);
        $this->assertInstanceOf('Magento\Object', $tabs['roles_section']);
        $this->assertEquals(array(
            'label' => 'User Role',
            'title' => 'User Role',
            'content' => 'Grid Block Content',
            'url' => '#',
            'id' => 'roles_section',
            'tab_id' => 'roles_section'
        ), $tabs['roles_section']->getData());
    }

    /**
     * Get protected _tabs property of \Magento\Backend\Block\Widget\Tabs block.
     *
     * @param \Magento\Backend\Block\Widget\Tabs $tabs
     * @return array
     */
    protected function _getProtectedTabsValue(\Magento\Backend\Block\Widget\Tabs $tabs)
    {
        $result = null;
        try {
            $classReflection = new \ReflectionClass(get_class($tabs));
            $tabsProperty = $classReflection->getProperty('_tabs');
            $tabsProperty->setAccessible(true);
            $result = $tabsProperty->getValue($tabs);
        } catch (\ReflectionException $exception) {
            $this->fail('Cannot get tabs value');

        }
        $this->assertInternalType('array', $result, 'Tabs value is expected to be an array');
        return $result;
    }
}

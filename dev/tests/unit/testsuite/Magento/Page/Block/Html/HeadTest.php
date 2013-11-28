<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Page\Block\Html;

class HeadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Page\Block\Html\Head
     */
    protected $_block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_pageAssets;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_pageAssets = $this->getMock('Magento\View\Asset\GroupedCollection', array(), array(), '', false);
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments(
            'Magento\Page\Block\Html\Head',
            array('assets' => $this->_pageAssets, 'objectManager' => $this->_objectManager)
        );
        $this->_block = $objectManagerHelper->getObject('Magento\Page\Block\Html\Head', $arguments);
    }

    protected function tearDown()
    {
        $this->_pageAssets = null;
        $this->_objectManager = null;
        $this->_block = null;
    }

    public function testAddRss()
    {
        $this->_pageAssets->expects($this->once())
            ->method('add')
            ->with(
                'link/http://127.0.0.1/test.rss',
                $this->isInstanceOf('Magento\View\Asset\Remote'),
                array('attributes' => 'rel="alternate" type="application/rss+xml" title="RSS Feed"')
            );
        $assetRemoteFile = $this->getMock('Magento\View\Asset\Remote', array(), array(), '', false);
        $this->_objectManager->expects($this->once(''))
            ->method('create')
            ->with('Magento\View\Asset\Remote')
            ->will($this->returnValue($assetRemoteFile));

        $this->_block->addRss('RSS Feed', 'http://127.0.0.1/test.rss');
    }
}

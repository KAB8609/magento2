<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Page_Block_Html_HeadTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Page_Block_Html_Head
     */
    protected $_block;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_pageAssets;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager');
        $this->_pageAssets = $this->getMock('Mage_Page_Model_Asset_GroupedCollection', array(), array(), '', false);
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $objectManagerHelper->getBlock(
            'Mage_Page_Block_Html_Head',
            array('page' => new Mage_Core_Model_Page($this->_pageAssets), 'objectManager' => $this->_objectManager)
        );
    }

    protected function tearDown()
    {
        $this->_pageAssets = null;
        $this->_objectManager = null;
        $this->_block = null;
    }

    public function testAddCss()
    {
        $this->_pageAssets->expects($this->once())
            ->method('add')
            ->with(
                Mage_Core_Model_Design_Package::CONTENT_TYPE_CSS . '/test.css',
                $this->isInstanceOf('Mage_Core_Model_Page_Asset_ViewFile')
            );
        $assetViewFile = $this->getMock('Mage_Core_Model_Page_Asset_ViewFile', array(), array(), '', false);
        $this->_objectManager->expects($this->once(''))
            ->method('create')
            ->with('Mage_Core_Model_Page_Asset_ViewFile')
            ->will($this->returnValue($assetViewFile));
        $this->_block->addCss('test.css');
    }

    public function testAddJs()
    {
        $this->_pageAssets->expects($this->once())
            ->method('add')
            ->with(
                Mage_Core_Model_Design_Package::CONTENT_TYPE_JS . '/test.js',
                $this->isInstanceOf('Mage_Core_Model_Page_Asset_ViewFile')
            );
        $assetViewFile = $this->getMock('Mage_Core_Model_Page_Asset_ViewFile', array(), array(), '', false);
        $this->_objectManager->expects($this->once(''))
            ->method('create')
            ->with('Mage_Core_Model_Page_Asset_ViewFile')
            ->will($this->returnValue($assetViewFile));
        $this->_block->addJs('test.js');
    }

    public function testAddRss()
    {
        $this->_pageAssets->expects($this->once())
            ->method('add')
            ->with(
                'link/http://127.0.0.1/test.rss',
                $this->isInstanceOf('Mage_Core_Model_Page_Asset_Remote'),
                array('attributes' => 'rel="alternate" type="application/rss+xml" title="RSS Feed"')
            );
        $assetRemoteFile = $this->getMock('Mage_Core_Model_Page_Asset_Remote', array(), array(), '', false);
        $this->_objectManager->expects($this->once(''))
            ->method('create')
            ->with('Mage_Core_Model_Page_Asset_Remote')
            ->will($this->returnValue($assetRemoteFile));

        $this->_block->addRss('RSS Feed', 'http://127.0.0.1/test.rss');
    }

    public function testAddLinkRel()
    {
        $this->_pageAssets->expects($this->once())
            ->method('add')
            ->with(
                'link/http://127.0.0.1/',
                $this->isInstanceOf('Mage_Core_Model_Page_Asset_Remote'),
                array('attributes' => 'rel="rel"')
            );
        $assetRemoteFile = $this->getMock('Mage_Core_Model_Page_Asset_Remote', array(), array(), '', false);
        $this->_objectManager->expects($this->once(''))
            ->method('create')
            ->with('Mage_Core_Model_Page_Asset_Remote')
            ->will($this->returnValue($assetRemoteFile));
        $this->_block->addLinkRel('rel', 'http://127.0.0.1/');
    }

    public function testRemoveItem()
    {
        $this->_pageAssets->expects($this->once())
            ->method('remove')
            ->with('css/test.css');
        $this->_block->removeItem('css', 'test.css');
    }
}

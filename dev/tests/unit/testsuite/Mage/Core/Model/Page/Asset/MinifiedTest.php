<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Page_Asset_MinifiedTest extends PHPUnit_Framework_TestCase
{
    const ORIG_SOURCE_FILE = 'original.js';
    const MINIFIED_SOURCE_FILE = 'original.min.js';
    const MINIFIED_URL = 'http://localhost/original.min.js';
    const ORIGINAL_URL = 'http://localhost/original.js';

    /**
     * @var Mage_Core_Model_Page_Asset_LocalInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_asset;

    /**
     * @var Magento_Code_Minifier|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_minifier;

    /**
     * @var Mage_Core_Model_Design_Package|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_designPackage;

    /**
     * @var Mage_Core_Model_Logger|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_logger;

    /**
     * @var Mage_Core_Model_Page_Asset_Minified
     */
    protected $_model;

    protected function setUp()
    {
        $this->_asset = $this->getMockForAbstractClass('Mage_Core_Model_Page_Asset_LocalInterface', array(), '', false);
        $this->_minifier = $this->getMock('Magento_Code_Minifier', array('getMinifiedFile'), array(), '', false);
        $this->_designPackage = $this->getMockForAbstractClass('Mage_Core_Model_Design_PackageInterface', array(), '',
            false);
        $this->_logger = $this->getMock('Mage_Core_Model_Logger', array(), array(), '', false);

        $this->_model = new Mage_Core_Model_Page_Asset_Minified($this->_asset, $this->_minifier, $this->_designPackage,
            $this->_logger);
    }

    protected function tearDown()
    {
        $this->_asset = null;
        $this->_minifier = null;
        $this->_designPackage = null;
        $this->_logger = null;
        $this->_model = null;
    }

    public function testGetUrl()
    {
        $this->_prepareProcessMock();
        $this->assertSame(self::MINIFIED_URL, $this->_model->getUrl());
        $this->assertSame(self::MINIFIED_URL, $this->_model->getUrl());
    }

    public function testGetSourceFile()
    {
        $this->_prepareProcessMock();
        $this->assertSame(self::MINIFIED_SOURCE_FILE, $this->_model->getSourceFile());
        $this->assertSame(self::MINIFIED_SOURCE_FILE, $this->_model->getSourceFile());
    }

    protected function _prepareProcessMock()
    {
        $this->_asset->expects($this->once())
            ->method('getSourceFile')
            ->will($this->returnValue(self::ORIG_SOURCE_FILE));
        $this->_minifier->expects($this->once())
            ->method('getMinifiedFile')
            ->with(self::ORIG_SOURCE_FILE)
            ->will($this->returnValue(self::MINIFIED_SOURCE_FILE));
        $this->_designPackage->expects($this->once())
            ->method('getPublicFileUrl')
            ->with(self::MINIFIED_SOURCE_FILE)
            ->will($this->returnValue(self::MINIFIED_URL));
    }

    public function testProcessException()
    {
        $this->_asset->expects($this->once())
            ->method('getSourceFile')
            ->will($this->returnValue(self::ORIG_SOURCE_FILE));
        $this->_asset->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue(self::ORIGINAL_URL));

        $this->_minifier->expects($this->once())
            ->method('getMinifiedFile')
            ->with(self::ORIG_SOURCE_FILE)
            ->will($this->throwException(new Exception('Error')));

        $this->_designPackage->expects($this->never())
            ->method('getPublicFileUrl');

        $this->assertSame(self::ORIGINAL_URL, $this->_model->getUrl());
        $this->assertSame(self::ORIG_SOURCE_FILE, $this->_model->getSourceFile());
    }

    public function testGetContent()
    {
        $contentType = 'content_type';
        $this->_asset->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue($contentType));
        $this->assertSame($contentType, $this->_model->getContentType());
    }
}
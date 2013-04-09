<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Page_Asset_MergedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Page_Asset_Merged
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_designPackage;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreHelper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_assetJsOne;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_assetJsTwo;

    protected function setUp()
    {
        $this->_assetJsOne = $this->getMockForAbstractClass('Mage_Core_Model_Page_Asset_MergeableInterface');
        $this->_assetJsOne->expects($this->any())->method('getContentType')->will($this->returnValue('js'));
        $this->_assetJsOne->expects($this->any())->method('getSourceFile')->will($this->returnValue('script_one.js'));

        $this->_assetJsTwo = $this->getMockForAbstractClass('Mage_Core_Model_Page_Asset_MergeableInterface');
        $this->_assetJsTwo->expects($this->any())->method('getContentType')->will($this->returnValue('js'));
        $this->_assetJsTwo->expects($this->any())->method('getSourceFile')->will($this->returnValue('script_two.js'));

        $this->_designPackage = $this->getMock('Mage_Core_Model_Design_PackageInterface');

        $this->_coreHelper = $this->getMock('Mage_Core_Helper_Data', array('isStaticFilesSigned'), array(), '', false);

        $this->_filesystem = $this->getMock('Magento_Filesystem', array('getMTime'), array(), '', false);

        $this->_object = new Mage_Core_Model_Page_Asset_Merged(
            $this->_designPackage, $this->_coreHelper, $this->_filesystem, array($this->_assetJsOne, $this->_assetJsTwo)
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage At least one asset has to be passed for merging.
     */
    public function testConstructorNothingToMerge()
    {
        $this->_object = new Mage_Core_Model_Page_Asset_Merged(
            $this->_designPackage, $this->_coreHelper, $this->_filesystem, array()
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Asset has to implement Mage_Core_Model_Page_Asset_MergeableInterface.
     */
    public function testConstructorRequireMergeInterface()
    {
        $assetUrl = new Mage_Core_Model_Page_Asset_Remote('http://example.com/style.css', 'css');
        $this->_object = new Mage_Core_Model_Page_Asset_Merged(
            $this->_designPackage, $this->_coreHelper, $this->_filesystem, array($this->_assetJsOne, $assetUrl)
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Content type 'css' cannot be merged with 'js'.
     */
    public function testConstructorIncompatibleContentTypes()
    {
        $assetCss = $this->getMockForAbstractClass('Mage_Core_Model_Page_Asset_MergeableInterface');
        $assetCss->expects($this->any())->method('getContentType')->will($this->returnValue('css'));
        $assetCss->expects($this->any())->method('getSourceFile')->will($this->returnValue('style.css'));

        $this->_object = new Mage_Core_Model_Page_Asset_Merged(
            $this->_designPackage, $this->_coreHelper, $this->_filesystem, array($this->_assetJsOne, $assetCss)
        );
    }

    public function testGetUrl()
    {
        $url = 'http://127.0.0.1/magento/merged.js';
        $this->_designPackage
            ->expects($this->at(0))
            ->method('mergeFiles')
            ->with(array('script_one.js', 'script_two.js'), 'js')
            ->will($this->returnValue('merged.js'))
        ;
        $this->_designPackage
            ->expects($this->at(1))
            ->method('getPublicFileUrl')
            ->with('merged.js')
            ->will($this->returnValue($url))
        ;
        $this->assertEquals($url, $this->_object->getUrl());
        $this->assertEquals($url, $this->_object->getUrl(), 'URL calculation should occur only once.');
    }

    public function testGetContentType()
    {
        $this->assertEquals('js', $this->_object->getContentType());
    }
}

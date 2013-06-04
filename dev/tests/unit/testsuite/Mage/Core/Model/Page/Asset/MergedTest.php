<?php
/**
 * {license_notice}
 *
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
    protected $_objectManager;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_logger;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirs;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mergeStrategy;

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
        $this->_assetJsOne->expects($this->any())->method('getSourceFile')
            ->will($this->returnValue('/pub/script_one.js'));

        $this->_assetJsTwo = $this->getMockForAbstractClass('Mage_Core_Model_Page_Asset_MergeableInterface');
        $this->_assetJsTwo->expects($this->any())->method('getContentType')->will($this->returnValue('js'));
        $this->_assetJsTwo->expects($this->any())->method('getSourceFile')
            ->will($this->returnValue('/pub/script_two.js'));

        $this->_logger = $this->getMock('Mage_Core_Model_Logger', array('logException'), array(), '', false);

        $this->_dirs = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);

        $this->_mergeStrategy = $this->getMock('Mage_Core_Model_Page_Asset_MergeStrategyInterface');

        $this->_objectManager = $this->getMockForAbstractClass(
            'Magento_ObjectManager', array(), '', true, true, true, array('create')
        );

        $this->_object = new Mage_Core_Model_Page_Asset_Merged(
            $this->_objectManager, $this->_logger, $this->_dirs, $this->_mergeStrategy,
            array($this->_assetJsOne, $this->_assetJsTwo)
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage At least one asset has to be passed for merging.
     */
    public function testConstructorNothingToMerge()
    {
        $this->_object = new Mage_Core_Model_Page_Asset_Merged(
            $this->_objectManager, $this->_logger, $this->_dirs, $this->_mergeStrategy, array()
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
            $this->_objectManager, $this->_logger, $this->_dirs, $this->_mergeStrategy,
            array($this->_assetJsOne, $assetUrl)
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
            $this->_objectManager, $this->_logger, $this->_dirs, $this->_mergeStrategy,
            array($this->_assetJsOne, $assetCss)
        );
    }

    public function testIteratorInterfaceMerge()
    {
        $mergedFile = '/_merged/19b2d7c942efeb2327eadbcf04635b02.js';

        $this->_logger->expects($this->never())->method('logException');

        $publicFiles = array(
            '/pub/script_one.js' => '/pub/script_one.js',
            '/pub/script_two.js' => '/pub/script_two.js'
        );

        $this->_mergeStrategy
            ->expects($this->once())
            ->method('mergeFiles')
            ->with($publicFiles, $mergedFile, 'js')
            ->will($this->returnValue(null));

        $mergedAsset = $this->getMockForAbstractClass('Mage_Core_Model_Page_Asset_MergeableInterface');
        $this->_objectManager
            ->expects($this->once())
            ->method('create')
            ->with('Mage_Core_Model_Page_Asset_PublicFile', array('file' => $mergedFile, 'contentType' => 'js'))
            ->will($this->returnValue($mergedAsset))
        ;

        $expectedResult = array($mergedAsset);
        $this->_assertIteratorEquals($expectedResult, $this->_object);
        $this->_assertIteratorEquals($expectedResult, $this->_object); // ensure merging happens only once
    }

    public function testIteratorInterfaceMergeFailure()
    {
        $mergeError = new Exception('File not found');
        $assetBroken = $this->getMockForAbstractClass('Mage_Core_Model_Page_Asset_MergeableInterface');
        $assetBroken->expects($this->any())->method('getContentType')->will($this->returnValue('js'));
        $assetBroken->expects($this->any())->method('getSourceFile')
            ->will($this->throwException($mergeError));

        $this->_object = new Mage_Core_Model_Page_Asset_Merged(
            $this->_objectManager, $this->_logger, $this->_dirs, $this->_mergeStrategy,
            array($this->_assetJsOne, $this->_assetJsTwo, $assetBroken)
        );


        $this->_objectManager->expects($this->never())->method('create');
        $this->_logger->expects($this->once())->method('logException')->with($this->identicalTo($mergeError));

        $expectedResult = array($this->_assetJsOne, $this->_assetJsTwo, $assetBroken);
        $this->_assertIteratorEquals($expectedResult, $this->_object);
        $this->_assertIteratorEquals($expectedResult, $this->_object); // ensure merging attempt happens only once
    }

    /**
     * Assert that iterator items equal to expected ones
     *
     * @param array $expectedItems
     * @param Iterator $actual
     */
    protected function _assertIteratorEquals(array $expectedItems, Iterator $actual)
    {
        $actualItems = array();
        foreach ($actual as $actualItem) {
            $actualItems[] = $actualItem;
        }
        $this->assertEquals($expectedItems, $actualItems);
    }
}

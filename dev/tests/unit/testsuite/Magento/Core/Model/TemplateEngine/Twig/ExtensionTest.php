<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_TemplateEngine_Twig_ExtensionTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Core_Model_TemplateEngine_Twig_Extension */
    protected $_extension;

    /** @var PHPUnit_Framework_MockObject_MockObject  */
    protected $_commonFunctionsMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_layoutFunctionsMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_translateMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_blockTrackerMock;

    protected function setUp()
    {
        $this->_blockTrackerMock = $this->getMockBuilder('Magento_Core_Model_TemplateEngine_BlockTrackerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_commonFunctionsMock = $this->getMockBuilder('Magento_Core_Model_TemplateEngine_Twig_CommonFunctions')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_layoutFunctionsMock = $this->getMockBuilder('Magento_Core_Model_TemplateEngine_Twig_LayoutFunctions')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_translateMock = $this->getMockBuilder('Magento_Core_Model_Translate')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_extension = new Magento_Core_Model_TemplateEngine_Twig_Extension(
            $this->_commonFunctionsMock,
            $this->_layoutFunctionsMock,
            $this->_translateMock
        );
        $this->_extension->setBlockTracker($this->_blockTrackerMock);
    }

    /**
     * Verify getName always returns 'Magento'
     */
    public function testGetName()
    {
        $this->assertSame('Magento', $this->_extension->getName(), 'Name should be Magento');
    }

    /**
     * Test that the getFunctions and getFilters return arrays of appropriate types
     */
    public function testGetFunctionsAndFilters()
    {
        $layoutFunc = array($this->getMockBuilder('Twig_SimpleFunction')->disableOriginalConstructor()->getMock());
        $commonFunc = array($this->getMockBuilder('Twig_SimpleFunction')->disableOriginalConstructor()->getMock());
        $expectedFunctions = array_merge($commonFunc, $layoutFunc);

        $this->_layoutFunctionsMock->expects($this->once())
            ->method('getFunctions')
            ->will($this->returnValue($layoutFunc));
        $this->_commonFunctionsMock->expects($this->once())
            ->method('getFunctions')
            ->will($this->returnValue($commonFunc));

        /** @var array $functions */
        $functions = $this->_extension->getFunctions();

        $this->assertInternalType('array', $functions);
        $this->assertTrue(count($functions) >= 1, 'Functions array does not contain any elements');
        $this->assertContainsOnly('Twig_SimpleFunction', $functions, false,
            'Contains something that is not a Twig function.');
        $this->assertEquals($expectedFunctions, $functions);

        /** @var array $filters */
        $filters = $this->_extension->getFilters();

        $this->assertInternalType('array', $filters);
        $this->assertTrue(count($filters) >= 1, 'Filters array does not contain any elements');
        $this->assertContainsOnly('Twig_SimpleFilter', $filters, false,
            'Contains something that is not a Twig filter.');
    }

    /**
     * Test __ (translate) function
     */
    public function testTranslate()
    {
        $translated = 'Guten Tag';

        $this->_translateMock->expects($this->once())
            ->method('translate')
            ->will($this->returnValue($translated));
        $actual = $this->_extension->translate('Good day');
        $this->assertEquals($translated, $actual, 'Translation did not work');
    }

}
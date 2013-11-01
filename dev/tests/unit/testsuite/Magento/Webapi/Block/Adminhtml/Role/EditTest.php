<?php
/**
 * Test class for \Magento\Webapi\Block\Adminhtml\Role\Edit
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Block\Adminhtml\Role;

class EditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\RequestInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var \Magento\Backend\Model\Url|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Helper\Data
     */
    protected $_escaper;

    /**
     * @var \Magento\Webapi\Block\Adminhtml\Role\Edit
     */
    protected $_block;

    protected function setUp()
    {
        $this->_urlBuilder = $this->getMockBuilder('Magento\Backend\Model\Url')
            ->setMethods(array('getUrl'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_request = $this->getMockBuilder('Magento\App\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_request->expects($this->any())
            ->method('getParam')
            ->with('role_id')
            ->will($this->returnValue(1));

        $this->_escaper = $this->getMockBuilder('Magento\Escaper')
            ->disableOriginalConstructor()
            ->setMethods(array('escapeHtml'))
            ->getMock();

        $context = $this->getMockBuilder('Magento\Backend\Block\Template\Context')
            ->disableOriginalConstructor()
            ->setMethods(array('getEscaper', 'getUrlBuilder', 'getRequest'))
            ->getMock();

        $context->expects($this->any())
            ->method('getEscaper')
            ->will($this->returnValue($this->_escaper));

        $context->expects($this->any())
            ->method('getUrlBuilder')
            ->will($this->returnValue($this->_urlBuilder));

        $context->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->_request));

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_block = $helper->getObject('Magento\Webapi\Block\Adminhtml\Role\Edit', array(
            'context' => $context
        ));
    }

    /**
     * Test _construct method.
     */
    public function testConstruct()
    {
        $this->assertAttributeEquals('Magento_Webapi', '_blockGroup', $this->_block);
        $this->assertAttributeEquals('adminhtml_role', '_controller', $this->_block);
        $this->assertAttributeEquals('role_id', '_objectId', $this->_block);
        $this->_assertBlockHasButton(1, 'save', 'Save API Role');
        $this->_assertBlockHasButton(0, 'delete', 'Delete API Role');
    }

    /**
     * Test getSaveAndContinueUrl method.
     */
    public function testGetSaveAndContinueUrl()
    {
        $expectedUrl = 'save_and_continue_url';
        $this->_urlBuilder
            ->expects($this->once())
            ->method('getUrl')
            ->with('adminhtml/*/save', array('_current' => true, 'continue' => true))
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_block->getSaveAndContinueUrl());
    }

    /**
     * Test getHeaderText method.
     */
    public function testGetHeaderText()
    {
        $apiRole = new \Magento\Object();
        $this->_block->setApiRole($apiRole);
        $this->assertEquals('New API Role', $this->_block->getHeaderText());

        $apiRole->setId(1)->setRoleName('Test Role');

        $this->_escaper->expects($this->once())
            ->method('escapeHtml')
            ->with($apiRole->getRoleName())
            ->will($this->returnArgument(0));

        $this->assertEquals("Edit API Role 'Test Role'", $this->_block->getHeaderText());
    }

    /**
     * Asserts that block has button with ID and label at level.
     *
     * @param int $level
     * @param string $buttonId
     * @param string $label
     */
    protected function _assertBlockHasButton($level, $buttonId, $label)
    {
        $buttonsProperty = new \ReflectionProperty($this->_block, '_buttons');
        $buttonsProperty->setAccessible(true);
        $buttons = $buttonsProperty->getValue($this->_block);
        $this->assertInternalType('array', $buttons, 'Cannot get block buttons.');
        $this->assertArrayHasKey($level, $buttons, "Block doesn't have buttons at level $level");
        $this->assertArrayHasKey($buttonId, $buttons[$level], "Block doesn't have '$buttonId' button at level $level");
        $this->assertArrayHasKey('label', $buttons[$level][$buttonId], "Block button doesn't have label.");
        $this->assertEquals($label, $buttons[$level][$buttonId]['label'], "Block button label has unexpected value.");
    }
}

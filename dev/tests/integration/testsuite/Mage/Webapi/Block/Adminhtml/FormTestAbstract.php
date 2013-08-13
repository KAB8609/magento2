<?php
/**
 * Abstract test case for Webapi forms. It was introduced to avoid copy-paste in form tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Webapi_Block_Adminhtml_FormTestAbstract extends PHPUnit_Framework_TestCase
{
    /**
     * Form class must be defined in children.
     *
     * @var string
     */
    protected $_formClass = '';

    /**
     * @var Mage_Webapi_Block_Adminhtml_User_Edit_Form
     */
    protected $_block;

    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Backend_Model_Url|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilder;

    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Magento_Core_Model_BlockFactory
     */
    protected $_blockFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->_objectManager = Mage::getObjectManager();
        $this->_urlBuilder = $this->getMockBuilder('Mage_Backend_Model_Url')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_layout = $this->_objectManager->get('Magento_Core_Model_Layout');
        $this->_blockFactory = $this->_objectManager->get('Magento_Core_Model_BlockFactory');
        $this->_block = $this->_blockFactory->createBlock($this->_formClass, array(
            'context' => Mage::getModel(
                'Mage_Backend_Block_Template_Context',
                array('urlBuilder' => $this->_urlBuilder)
            )
        ));
        $this->_layout->addBlock($this->_block);
    }

    protected function tearDown()
    {
        $this->_objectManager->removeSharedInstance('Magento_Core_Model_Layout');
        unset($this->_objectManager, $this->_urlBuilder, $this->_layout, $this->_blockFactory, $this->_block);
    }

    /**
     * Test _prepareForm method.
     */
    public function testPrepareForm()
    {
        // TODO: Move to unit tests after MAGETWO-4015 complete.
        $this->assertEmpty($this->_block->getForm());

        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/*/save', array())
            ->will($this->returnValue('action_url'));
        $this->_block->toHtml();

        $form = $this->_block->getForm();
        $this->assertInstanceOf('Magento_Data_Form', $form);
        $this->assertTrue($form->getUseContainer());
        $this->assertEquals('edit_form', $form->getId());
        $this->assertEquals('post', $form->getMethod());
        $this->assertEquals('action_url', $form->getAction());
    }
}

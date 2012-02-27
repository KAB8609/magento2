<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_DesignEditor
 */
class Mage_DesignEditor_Block_ToolbarTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Block_Toolbar
     */
    protected $_block;

    protected function setUp()
    {
        $layout = Mage::app()->getLayout();
        $this->_block = $layout->createBlock(
            'Mage_DesignEditor_Block_Toolbar',
            'block',
            array('template' => 'toolbar.phtml')
        );
        $layout->insertBlock('', 'block', 'block');
    }

    /**
     * Isolation has been raised because block pollutes the registry
     *
     * @magentoAppIsolation enabled
     */
    public function testToHtmlDesignEditorInactive()
    {
        $this->assertEmpty($this->_block->toHtml());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testToHtmlDesignEditorActive()
    {
        $this->assertNotEmpty($this->_block->toHtml());
        $this->assertContains('title="Exit" class="vde_button">', $this->_block->toHtml());
    }

    public function testGetExitUrl()
    {
        $expected = 'http://localhost/index.php/admin/system_design_editor/exit/';
        $this->assertContains($expected, $this->_block->getExitUrl());
    }

    public function testGetMessages()
    {
        /** @var $session Mage_DesignEditor_Model_Session */
        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        $this->assertEmpty($session->getMessages()->getItems());

        $session->addError('test error');
        $session->addSuccess('test success');

        $blockMessages = $this->_block->getMessages();
        $this->assertInternalType('array', $blockMessages);
        $this->assertEquals(2, count($blockMessages));

        $this->assertInstanceOf('Mage_Core_Model_Message_Error', $blockMessages[0]);
        $this->assertEquals('test error', $blockMessages[0]->getCode());
        $this->assertInstanceOf('Mage_Core_Model_Message_Success', $blockMessages[1]);
        $this->assertEquals('test success', $blockMessages[1]->getCode());

        $this->assertEmpty($session->getMessages()->getItems());
    }
}

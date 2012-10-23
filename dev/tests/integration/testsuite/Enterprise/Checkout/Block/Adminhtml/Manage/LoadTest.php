<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Checkout_Block_Adminhtml_Manage_LoadTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Core_Model_Layout */
    protected $_layout = null;

    /** @var Enterprise_Checkout_Block_Adminhtml_Manage_Load */
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = Mage::getModel('Mage_Core_Model_Layout');
        $this->_block = $this->_layout->createBlock('Enterprise_Checkout_Block_Adminhtml_Manage_Load');
    }

    protected function tearDown()
    {
        $this->_block = null;
        $this->_layout = null;
    }

    public function testToHtml()
    {
        $blockName        = 'block1';
        $blockNameOne     = 'block2';
        $containerName    = 'container';
        $content          = 'Content 1';
        $contentOne       = 'Content 2';
        $containerContent = 'Content in container';

        $parent = $this->_block->getNameInLayout();
        $this->_layout->addBlock('Mage_Core_Block_Text', $blockName, $parent)->setText($content);
        $this->_layout->addContainer($containerName, 'Container', array(), $parent);
        $this->_layout->addBlock('Mage_Core_Block_Text', '', $containerName)->setText($containerContent);
        $this->_layout->addBlock('Mage_Core_Block_Text', $blockNameOne, $parent)->setText($contentOne);

        $result = $this->_block->toHtml();
        $expectedDecoded = array(
            $blockName       => $content,
            $containerName   => $containerContent,
            $blockNameOne    => $contentOne
        );
        $this->assertEquals($expectedDecoded, Mage::helper('Mage_Core_Helper_Data')->jsonDecode($result));
    }
}

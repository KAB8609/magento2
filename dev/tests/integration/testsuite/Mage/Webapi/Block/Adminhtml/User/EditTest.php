<?php
/**
 * Test for Mage_Webapi_Block_Adminhtml_User_Edit block.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Block_Adminhtml_User_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Mage_Webapi_Block_Adminhtml_User_Edit
     */
    protected $_block;

    /**
     * Initialize block.
     */
    protected function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();
        $this->_layout = Mage::getObjectManager()->get('Mage_Core_Model_Layout');
        $this->_block = $this->_layout->createBlock('Mage_Webapi_Block_Adminhtml_User_Edit');
    }

    /**
     * Clear block.
     */
    protected function tearDown()
    {
        unset($this->_objectManager, $this->_layout, $this->_block);
    }

    /**
     * Test _beforeToHtml method.
     */
    public function testBeforeToHtml()
    {
        // TODO: Move to unit tests after MAGETWO-4015 complete.
        $apiUser = new Varien_Object();
        $this->_block->setApiUser($apiUser);
        $this->_block->toHtml();
        $this->assertSame($apiUser, $this->_block->getChildBlock('form')->getApiUser());
    }
}

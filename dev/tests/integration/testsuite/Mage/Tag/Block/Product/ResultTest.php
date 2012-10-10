<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Tag_Block_Product_ResultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Tag_Block_Product_Result
     */
    protected $_block = null;

    /**
     * @var Mage_Core_Model_Layout
     */
    protected $_layout = null;

    /**
     * @var Mage_Core_Block_Text
     */
    protected $_child = null;

    public static function setUpBeforeClass()
    {
        Mage::register('current_tag', new Varien_Object(array('id' => uniqid())));
    }

    protected function setUp()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        $this->_block = Mage::app()->getLayout()->createBlock('Mage_Tag_Block_Product_Result');
        $this->_layout = Mage::getModel('Mage_Core_Model_Layout');
        $this->_layout->addBlock('Mage_Core_Block_Text', 'root');
        $this->_layout->addBlock('Mage_Core_Block_Text', 'head');
        $this->_layout->addBlock($this->_block, 'test');
        $this->_child = Mage::app()->getLayout()->createBlock('Mage_Core_Block_Text');
        $this->_layout->addBlock($this->_child, 'search_result_list', 'test');
    }

    protected function tearDown()
    {
        $this->_block = null;
        $this->_layout = null;
        $this->_child = null;
    }

    public function testSetListOrders()
    {
        $this->assertEmpty($this->_child->getData('available_orders'));
        $this->_block->setListOrders();
        $this->assertNotEmpty($this->_child->getData('available_orders'));
    }

    public function testSetListModes()
    {
        $this->assertEmpty($this->_child->getData('modes'));
        $this->_block->setListModes();
        $this->assertNotEmpty($this->_child->getData('modes'));
    }

    public function testSetListCollection()
    {
        $this->assertEmpty($this->_child->getData('collection'));
        $this->_block->setListCollection();
        $this->assertNotEmpty($this->_child->getData('collection'));
    }
}

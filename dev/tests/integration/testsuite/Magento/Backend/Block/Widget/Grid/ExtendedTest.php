<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Block_Widget_Grid_ExtendedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Block_Widget_Grid_Extended
     */
    protected $_block;

    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layoutMock;

    protected function setUp()
    {
        parent::setUp();

        $this->_layoutMock = Mage::getModel('Magento_Core_Model_Layout');
        $context = Mage::getModel('Magento_Backend_Block_Template_Context', array('layout' => $this->_layoutMock));
        $this->_block = $this->_layoutMock->createBlock(
            'Magento_Backend_Block_Widget_Grid_Extended', 'grid', array('context' => $context)
        );

        $this->_block->addColumn('column1',
            array('id' => 'columnId1')
        );
        $this->_block->addColumn('column2',
            array('id' => 'columnId2')
        );
    }

    public function testAddColumnAddsChildToColumnSet()
    {
        $this->assertInstanceOf(
            'Magento_Backend_Block_Widget_Grid_Column',
            $this->_block->getColumnSet()->getChildBlock('column1')
        );
        $this->assertCount(2, $this->_block->getColumnSet()->getChildNames());
    }

    public function testRemoveColumn()
    {
        $this->assertCount(2, $this->_block->getColumnSet()->getChildNames());
        $this->_block->removeColumn('column1');
        $this->assertCount(1, $this->_block->getColumnSet()->getChildNames());
    }

    public function testSortColumnsByOrder()
    {
        $columnNames = $this->_block->getLayout()->getChildNames($this->_block->getColumnSet()->getNameInLayout());
        $this->assertEquals($this->_block->getColumn('column1')->getNameInLayout(), $columnNames[0]);
        $this->_block->addColumnsOrder('column1', 'column2');
        $this->_block->sortColumnsByOrder();
        $columnNames = $this->_block->getLayout()->getChildNames($this->_block->getColumnSet()->getNameInLayout());
        $this->assertEquals($this->_block->getColumn('column2')->getNameInLayout(), $columnNames[0]);
    }

    public function testGetMainButtonsHtmlReturnsEmptyStringIfFiltersArentVisible()
    {
        $this->_block->setFilterVisibility(false);
        $this->assertEquals('', $this->_block->getMainButtonsHtml());
    }
}
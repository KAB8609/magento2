<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Block_Widget_GridTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Block_Widget_Grid_ColumnSet
     */
    protected $_block;

    /**
     * @var Mage_Core_Model_Layout|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutMock;

    /**
     * @var Mage_Backend_Block_Widget_Grid_ColumnSet|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_columnSetMock;

    protected function setUp()
    {
        $this->_layoutMock = $this->getMock('Mage_Core_Model_Layout', array(), array(), '', false);
        $this->_columnSetMock = $this->_getColumnSetMock();

        $returnValueMap = array(
            array('grid', 'grid.columnSet', 'grid.columnSet'),
            array('grid', 'reset_filter_button', 'reset_filter_button'),
            array('grid', 'search_button', 'search_button')
        );
        $this->_layoutMock->expects($this->any())->method('getChildName')
            ->will($this->returnValueMap($returnValueMap));
        $this->_layoutMock->expects($this->any())->method('getBlock')
            ->with('grid.columnSet')
            ->will($this->returnValue($this->_columnSetMock));
        $this->_layoutMock->expects($this->any())->method('createBlock')
            ->with('Mage_Backend_Block_Widget_Button')
            ->will($this->returnValue(Mage::app()->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')));
        $this->_layoutMock->expects($this->any())->method('helper')
            ->with('Mage_Core_Helper_Data')
            ->will($this->returnValue(Mage::helper('Mage_Core_Helper_Data')));


        $this->_block = Mage::app()->getLayout()->createBlock('Mage_Backend_Block_Widget_Grid');
        $this->_block->setLayout($this->_layoutMock);
        $this->_block->setNameInLayout('grid');
    }

    protected function tearDown()
    {
        $this->_block = null;
        $this->_layoutMock = null;
        $this->_columnSetMock = null;
    }

    /**
     * Retrieve the mocked column set block instance
     *
     * @return Mage_Backend_Block_Widget_Grid_ColumnSet|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getColumnSetMock()
    {
        return $this->getMock('Mage_Backend_Block_Widget_Grid_ColumnSet', array(), array(
            Mage::getModel('Mage_Core_Controller_Request_Http'),
            Mage::getModel('Mage_Core_Model_Layout'),
            Mage::getModel('Mage_Core_Model_Event_Manager'),
            Mage::getModel('Mage_Backend_Model_Url'),
            Mage::getModel('Mage_Core_Model_Translate'),
            Mage::getModel('Mage_Core_Model_Cache'),
            Mage::getModel('Mage_Core_Model_Design_Package'),
            Mage::getModel('Mage_Core_Model_Session'),
            Mage::getModel('Mage_Core_Model_Store_Config'),
            Mage::getModel('Mage_Core_Controller_Varien_Front'),
            Mage::getModel('Mage_Core_Model_Factory_Helper'),
            new Mage_Core_Model_Dir(__DIR__, new Varien_Io_File()),
            Mage::getModel('Mage_Core_Model_Logger'),
            new Magento_Filesystem(new Magento_Filesystem_Adapter_Local),
            Mage::getModel('Mage_Backend_Helper_Data'),
            Mage::getModel('Mage_Backend_Model_Widget_Grid_Row_UrlGeneratorFactory'),
            Mage::getModel('Mage_Backend_Model_Widget_Grid_SubTotals'),
            Mage::getModel('Mage_Backend_Model_Widget_Grid_Totals'),
        ));
    }

    public function testToHtmlPreparesColumns()
    {
        $this->_columnSetMock->expects($this->once())->method('setRendererType');
        $this->_columnSetMock->expects($this->once())->method('setFilterType');
        $this->_columnSetMock->expects($this->once())->method('setSortable');
        $this->_block->setColumnRenderers(array('filter' => 'Filter_Class'));
        $this->_block->setColumnFilters(array('filter' => 'Filter_Class'));
        $this->_block->setSortable(false);
        $this->_block->toHtml();
    }

    public function testGetMainButtonsHtmlReturnsEmptyStringIfFiltersArentVisible()
    {
        $this->_columnSetMock->expects($this->once())->method('isFilterVisible')->will($this->returnValue(false));
        $this->_block->getMainButtonsHtml();
    }
}

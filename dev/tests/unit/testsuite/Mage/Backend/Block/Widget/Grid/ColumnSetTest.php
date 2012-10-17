<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Block_Widget_Grid_ColumnSetTest extends Magento_Test_TestCase_ObjectManagerAbstract
{
    /**
     * @var Mage_Backend_Block_Widget_Grid_ColumnSet
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_columnMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    protected function setUp()
    {
        $this->_columnMock = $this->getMock('Mage_Backend_Block_Widget_Grid_Column',
            array('setSortable', 'setRendererType', 'setFilterType'), array(), '', false);
        $this->_layoutMock = $this->getMock('Mage_Core_Model_Layout', array(), array(), '', false);
        $this->_layoutMock
            ->expects($this->any())
            ->method('getChildBlocks')
            ->will($this->returnValue(array($this->_columnMock)));
        $this->_helperMock = $this->getMock('Mage_Backend_Helper_Data', array(), array(), '', false);
        $this->_helperMock
            ->expects($this->any())
            ->method('__')
            ->will($this->returnValue('TRANSLATED STRING'));
        $this->_factoryMock = $this->getMock('Mage_Backend_Model_Widget_Grid_Row_UrlGeneratorFactory', array(), array(),
            '', false
        );

        $arguments = array(
            'layout'           => $this->_layoutMock,
            'helper'           => $this->_helperMock,
            'generatorFactory' => $this->_factoryMock
        );
        $arguments = $this->_getConstructArguments(self::BLOCK_ENTITY, 'Mage_Backend_Block_Widget_Grid_ColumnSet',
            $arguments
        );
        $this->_model = $this->_getInstanceViaConstructor('Mage_Backend_Block_Widget_Grid_ColumnSet', $arguments);
    }

    public function tearDown()
    {
        unset($this->_model);
        unset($this->_layoutMock);
        unset($this->_columnMock);
        unset($this->_helperMock);
        unset($this->_factoryMock);
    }

    public function testSetSortablePropagatesSortabilityToChildren()
    {
        $this->_columnMock->expects($this->once())->method('setSortable')->with(false);
        $this->_model->setSortable(false);
    }

    public function testSetSortablePropagatesSortabilityToChildrenOnlyIfSortabilityIsFalse()
    {
        $this->_columnMock->expects($this->never())->method('setSortable');
        $this->_model->setSortable(true);
    }

    public function testSetRendererTypePropagatesRendererTypeToColumns()
    {
        $this->_columnMock->expects($this->once())->method('setRendererType')->with('renderer', 'Renderer_Class');
        $this->_model->setRendererType('renderer', 'Renderer_Class');
    }

    public function testSetFilterTypePropagatesFilterTypeToColumns()
    {
        $this->_columnMock->expects($this->once())->method('setFilterType')->with('filter', 'Filter_Class');
        $this->_model->setFilterType('filter', 'Filter_Class');
    }

    public function testGetRowUrlIfUrlPathNotSet()
    {
        $this->assertEquals('#', $this->_model->getRowUrl(new StdClass()));
    }

    public function testGetRowUrl()
    {
        $generatorClass = 'Mage_Backend_Model_Widget_Grid_Row_UrlGenerator';

        $itemMock = $this->getMock('Varien_Object', array(), array(), '', false);

        $rowUrlGenerator = $this->getMock('Mage_Backend_Model_Widget_Grid_Row_UrlGenerator', array('getUrl'), array(),
            '', false
        );
        $rowUrlGenerator->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo($itemMock))
            ->will($this->returnValue('http://localhost/mng/item/edit'));

        $factoryMock = $this->getMock('Mage_Backend_Model_Widget_Grid_Row_UrlGeneratorFactory',
            array('createUrlGenerator'), array(), '', false
        );
        $factoryMock->expects($this->once())
            ->method('createUrlGenerator')
            ->with($this->equalTo($generatorClass),
                $this->equalTo(array('args' => array('generatorClass' => $generatorClass)))
            )
            ->will($this->returnValue($rowUrlGenerator));

        $arguments = array(
            'layout'           => $this->_layoutMock,
            'helper'           => $this->_helperMock,
            'generatorFactory' => $factoryMock,
            'data'             => array(
                'rowUrl' => array('generatorClass' => $generatorClass)
            )
        );
        $arguments = $this->_getConstructArguments(self::BLOCK_ENTITY, 'Mage_Backend_Block_Widget_Grid_ColumnSet',
            $arguments
        );
        $model = $this->_getInstanceViaConstructor('Mage_Backend_Block_Widget_Grid_ColumnSet', $arguments);

        $url = $model->getRowUrl($itemMock);
        $this->assertEquals('http://localhost/mng/item/edit', $url);
    }
}

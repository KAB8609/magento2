<?php
/**
 * {license_notice}
 *
 * @category    Varien
 * @package     Varien_Data
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Varien_Data_Collection_DbTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Varien_Data_Collection_Db
     */
    protected $_collection;

    protected function setUp()
    {
        $this->_collection = new Varien_Data_Collection_Db;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Zend_Db_Adapter_Abstract
     */
    public function testSetAddOrder()
    {
        $adapter = $this->_getAdapterMock('Zend_Db_Adapter_Pdo_Mysql', array('fetchAll'), null);
        $this->_collection->setConnection($adapter);

        $select = $this->_collection->getSelect();
        $this->assertEmpty($select->getPart(Zend_Db_Select::ORDER));

        /* Direct access to select object is available and many places are using it for sort order declaration */
        $select->order('select_field', Varien_Data_Collection::SORT_ORDER_ASC);
        $this->_collection->addOrder('some_field', Varien_Data_Collection::SORT_ORDER_ASC);
        $this->_collection->setOrder('other_field', Varien_Data_Collection::SORT_ORDER_ASC);
        $this->_collection->addOrder('other_field', Varien_Data_Collection::SORT_ORDER_DESC);

        $this->_collection->load();
        $selectOrders = $select->getPart(Zend_Db_Select::ORDER);
        $this->assertEquals(array('select_field', 'ASC'), array_shift($selectOrders));
        $this->assertEquals('some_field ASC', (string)array_shift($selectOrders));
        $this->assertEquals('other_field DESC', (string)array_shift($selectOrders));
        $this->assertEmpty(array_shift($selectOrders));

        return $adapter;
    }

    /**
     * @param PHPUnit_Framework_MockObject_MockObject|Zend_Db_Adapter_Abstract $adapter
     * @depends testSetAddOrder
     */
    public function testUnshiftOrder($adapter)
    {
        $this->_collection->setConnection($adapter);
        $this->_collection->addOrder('some_field', Varien_Data_Collection::SORT_ORDER_ASC);
        $this->_collection->unshiftOrder('other_field', Varien_Data_Collection::SORT_ORDER_ASC);

        $this->_collection->load();
        $selectOrders = $this->_collection->getSelect()->getPart(Zend_Db_Select::ORDER);
        $this->assertEquals('other_field ASC', (string)array_shift($selectOrders));
        $this->assertEquals('some_field ASC', (string)array_shift($selectOrders));
        $this->assertEmpty(array_shift($selectOrders));
    }

    /**
     * Create an adapter mock object
     *
     * @param string $adapterClass
     * @param array $mockMethods
     * @param array|null $constructArgs
     * @param string $mockStatementMethods
     * @return Zend_Db_Adapter_Abstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getAdapterMock($adapterClass, $mockMethods, $constructArgs = array(),
        $mockStatementMethods = 'execute'
    ) {
        if (null == $constructArgs) {
            $adapter = $this->getMock($adapterClass, $mockMethods, array(), '', false);
        } else {
            $adapter = $this->getMock($adapterClass, $mockMethods, $constructArgs);
        }
        if (null !== $mockStatementMethods) {
            $statement = $this->getMock('Zend_Db_Statement', array_merge((array)$mockStatementMethods,
                    array('closeCursor', 'columnCount', 'errorCode', 'errorInfo', 'fetch', 'nextRowset', 'rowCount')
                ), array(), '', false
            );
            $adapter->expects($this->any())
                    ->method('query')
                    ->will($this->returnValue($statement));
        }
        return $adapter;
    }

    /**
     * Test that adding field to filter builds proper sql WHERE condition
     */
    public function testAddFieldToFilter()
    {
        $adapter =$this->_getAdapterMock(
            'Zend_Db_Adapter_Pdo_Mysql',
            array('fetchAll', 'prepareSqlCondition'),
            null
        );
        $adapter->expects($this->any())
            ->method('prepareSqlCondition')
            ->with(
                $this->stringContains('is_imported'),
                $this->anything()
            )
            ->will($this->returnValue('is_imported = 1'));
        $this->_collection->setConnection($adapter);
        $select = $this->_collection->getSelect()->from('test');

        $this->_collection->addFieldToFilter('is_imported', array('eq' => '1'));
        $this->assertEquals('SELECT `test`.* FROM `test` WHERE (is_imported = 1)', $select->assemble());
    }

    /**
     * Test that adding multiple fields to filter at once
     * builds proper sql WHERE condition and created conditions are joined with OR
     */
    public function testAddFieldToFilterWithMultipleParams()
    {
        $adapter = $this->_getAdapterMock(
            'Zend_Db_Adapter_Pdo_Mysql',
            array('fetchAll', 'prepareSqlCondition'),
            null
        );
        $adapter->expects($this->at(0))
            ->method('prepareSqlCondition')
            ->with(
                'weight',
                array('in' => array(1,3))
            )
            ->will($this->returnValue('weight in (1, 3)'));
        $adapter->expects($this->at(1))
            ->method('prepareSqlCondition')
            ->with(
                'name',
                array('like' => 'M%')
            )
            ->will($this->returnValue("name like 'M%'"));
        $this->_collection->setConnection($adapter);
        $select = $this->_collection->getSelect()->from("test");

        $this->_collection->addFieldToFilter(
            array('weight', 'name'),
            array(array('in' => array(1,3)), array('like' => 'M%'))
        );

        $this->assertEquals(
            "SELECT `test`.* FROM `test` WHERE ((weight in (1, 3)) OR (name like 'M%'))",
            $select->assemble()
        );

        $adapter->expects($this->at(0))
            ->method('prepareSqlCondition')
            ->with(
                'is_imported',
                $this->anything()
            )
            ->will($this->returnValue('is_imported = 1'));

        $this->_collection->addFieldToFilter('is_imported', array('eq' => '1'));
        $this->assertEquals(
            "SELECT `test`.* FROM `test` WHERE ((weight in (1, 3)) OR (name like 'M%')) AND (is_imported = 1)",
            $select->assemble()
        );
    }
}

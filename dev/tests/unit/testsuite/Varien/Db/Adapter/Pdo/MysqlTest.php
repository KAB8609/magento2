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

/**
 * Varien_Db_Adapter_Pdo_Mysql class test
 */
class Varien_Db_Adapter_Pdo_MysqlTest extends PHPUnit_Framework_TestCase
{
    /**
     * Error message for DDL query in transactions
     */
    const ERROR_DDL_MESSAGE = 'DDL statements are not allowed in transactions';

    /**
     * Adapter for test
     * @var Varien_Db_Adapter_Pdo_Mysql
     */
    private $_adapter;

    /**
     * Setup
     */
    protected function setUp()
    {
        $this->_adapter = new Varien_Db_Adapter_Pdo_Mysql(
            array(
                'dbname' => 'not_exists',
                'username' => 'not_valid',
                'password' => 'not_valid',
            )
        );
    }

    /**
     * Test result for bigint
     *
     * @dataProvider bigintResultProvider
     */
    public function testPrepareColumnValueForBigint($value, $expectedResult)
    {
        $result = $this->_adapter->prepareColumnValue(
            array('DATA_TYPE' => 'bigint'),
            $value
        );
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Data Provider for testPrepareColumnValueForBigint
     */
    public function bigintResultProvider()
    {
        return array(
            array(1, 1),
            array(0, 0),
            array(-1, -1),
            array(1.0, 1),
            array(0.0, 0),
            array(-1.0, -1),
            array(1e-10, 0),
            array(7.9, 8),
            array(PHP_INT_MAX, PHP_INT_MAX),
            array(PHP_INT_MAX+1, '2147483648'),
            array(9223372036854775807, '9223372036854775807'),
            array(9223372036854775807.3423424234, '9223372036854775807'),
            array(PHP_INT_MAX*pow(10, 10)+12, '21474836470000000012'),
            array((0.099999999999999999999999995+0.2+0.3+0.4+0.5)*10, '15'),
            array('21474836470000000012', '21474836470000000012'),
            array(0x5468792130ABCDEF, '6082244480221302255')
        );
    }

    /**
     * Test DDL query in transaction
     */
    public function testCheckDdlTransaction()
    {
        $mockAdapter = $this->getMock(
            'Varien_Db_Adapter_Pdo_Mysql',
            array('beginTransaction', 'getTransactionLevel'),
            array(), '', false
        );

        $mockAdapter->expects($this->any())
             ->method('getTransactionLevel')
             ->will($this->returnValue(1));

        $mockAdapter->beginTransaction();
        try {
            $mockAdapter->query("CREATE table user");
        } catch (Zend_Db_Adapter_Exception $e) {
            $this->assertEquals($e->getMessage(), self::ERROR_DDL_MESSAGE);
        }

        try {
            $mockAdapter->query("ALTER table user");
        } catch (Zend_Db_Adapter_Exception $e) {
            $this->assertEquals($e->getMessage(), self::ERROR_DDL_MESSAGE);
        }

        try {
            $mockAdapter->query("TRUNCATE table user");
        } catch (Zend_Db_Adapter_Exception $e) {
            $this->assertEquals($e->getMessage(), self::ERROR_DDL_MESSAGE);
        }

        try {
            $mockAdapter->query("RENAME table user");
        } catch (Zend_Db_Adapter_Exception $e) {
            $this->assertEquals($e->getMessage(), self::ERROR_DDL_MESSAGE);
        }

        try {
            $mockAdapter->query("DROP table user");
        } catch (Zend_Db_Adapter_Exception $e) {
            $this->assertEquals($e->getMessage(), self::ERROR_DDL_MESSAGE);
        }

        try {
            $mockAdapter->query("SELECT * FROM user");
        } catch (Exception $e) {
            $this->assertFalse($e instanceof Zend_Db_Adapter_Exception);
        }

        $select = new Zend_Db_Select($mockAdapter);
        $select->from('user');
        try {
            $mockAdapter->query($select);
        } catch (Exception $e) {
            $this->assertFalse($e instanceof Zend_Db_Adapter_Exception);
        }
    }
}

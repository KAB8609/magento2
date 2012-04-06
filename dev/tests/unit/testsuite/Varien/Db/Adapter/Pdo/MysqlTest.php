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
     * Custom error handler message
     */
    const CUSTOM_ERROR_HANDLER_MESSAGE = 'Custom error handler message';

    /**
     * Adapter for test
     * @var Varien_Db_Adapter_Pdo_Mysql
     */
    private $_adapter;

    /*
     * Mock DB adapter for DDL query tests
     */
    private $_mockAdapter;

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

        $this->_mockAdapter = $this->getMock(
            'Varien_Db_Adapter_Pdo_Mysql',
            array('beginTransaction', 'getTransactionLevel'),
            array(), '', false
        );

        $this->_mockAdapter->expects($this->any())
             ->method('getTransactionLevel')
             ->will($this->returnValue(1));
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
            array(2147483647 + 1, '2147483648'),
            array(9223372036854775807 + 1, '9223372036854775808'),
            array(9223372036854775807, '9223372036854775807'),
            array(9223372036854775807.3423424234, '9223372036854775807'),
            array(2147483647 * pow(10, 10)+12, '21474836470000000012'),
            array(9223372036854775807 * pow(10, 10)+12, '92233720368547758080000000000'),
            array((0.099999999999999999999999995+0.2+0.3+0.4+0.5)*10, '15'),
            array('21474836470000000012', '21474836470000000012'),
            array(0x5468792130ABCDEF, '6082244480221302255')
        );
    }

    /**
     * Test not DDL query inside transaction
     *
     * @dataProvider sqlQueryProvider
     */
    public function testCheckNotDdlTransaction($developerMode, $query)
    {
        Mage::setIsDeveloperMode($developerMode);
        if ($developerMode) {
            $this->assertTrue(Mage::getIsDeveloperMode());
        } else {
            $this->assertFalse(Mage::getIsDeveloperMode());
        }

        try {
            $this->_mockAdapter->query($query);
        } catch (Exception $e) {
            $this->assertTrue(strpos($e->getMessage(), Varien_Db_Adapter_Interface::ERROR_DDL_MESSAGE) === false);
        }

        $select = new Zend_Db_Select($this->_mockAdapter);
        $select->from('user');
        try {
            $this->_mockAdapter->query($select);
        } catch (Exception $e) {
            $this->assertTrue(strpos($e->getMessage(), Varien_Db_Adapter_Interface::ERROR_DDL_MESSAGE) === false);
        }
    }

    /**
     * Test DDL query inside transaction in Developer mode
     *
     * @dataProvider ddlSqlQueryProvider
     */
    public function testCheckDdlTransactionDeveloperMode($ddlQuery)
    {
        set_error_handler(array(
            'Varien_Db_Adapter_Pdo_MysqlTest',
            'errorHandler'
        ));

        Mage::setIsDeveloperMode(true);
        $this->assertTrue(Mage::getIsDeveloperMode());

        try {
            $this->_mockAdapter->query($ddlQuery);
        } catch (Exception $e) {
            $this->assertTrue(strpos($e->getMessage(), Varien_Db_Adapter_Interface::ERROR_DDL_MESSAGE) !== false);
        }

        restore_error_handler();
    }

    /**
     * Test DDL query inside transaction Not in Developer mode
     *
     * @dataProvider ddlSqlQueryProvider
     */
    public function testCheckDdlTransactionNotDeveloperMode($ddlQuery)
    {
        set_error_handler(array(
            'Varien_Db_Adapter_Pdo_MysqlTest',
            'errorHandler'
        ));

        Mage::setIsDeveloperMode(false);
        $this->assertFalse(Mage::getIsDeveloperMode());

        try {
            $this->_mockAdapter->query($ddlQuery);
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), self::CUSTOM_ERROR_HANDLER_MESSAGE);
        }

        restore_error_handler();
    }

    /**
     * Data Provider for testCheckDdlTransaction
     */
    public static function ddlSqlQueryProvider()
    {
        return array(
            array('CREATE table user'),
            array('ALTER table user'),
            array('TRUNCATE table user'),
            array('RENAME table user'),
            array('DROP table user'),
        );
    }

    /**
     * Data Provider for testCheckNotDdlTransaction
     */
    public static function sqlQueryProvider()
    {
        return array(
            array(false, 'SELECT * FROM user'),
            array(false, 'UPDATE user'),
            array(false, 'DELETE from user'),
            array(false, 'INSERT into user'),
            array(true, 'SELECT * FROM user'),
            array(true, 'UPDATE user'),
            array(true, 'DELETE from user'),
            array(true, 'INSERT into user'),
        );
    }

    /**
     * Custom Error handler function
     */
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        call_user_func(Mage_Core_Model_App::DEFAULT_ERROR_HANDLER,
            $errno, $errstr, $errfile, $errline
        );
        throw new Exception(self::CUSTOM_ERROR_HANDLER_MESSAGE);
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Helper;

/**
 * Class CliTest
 *
 * @package Magento\Test\Helper
 */
class CliTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Getopt object
     *
     * @var \Zend_Console_Getopt
     */
    protected $_getOpt;

    /**
     * Param constants
     */
    const TEST_OPTION_NAME  = 'name';
    const TEST_OPTION_VALUE = 'test_option_value';

    /**
     * Set up before test
     */
    public function setUp()
    {

        $this->_getOpt =  $this->getMock(
            'Zend_Console_Getopt',
            array('getOption'),
            array(array())
        );
        $this->_getOpt->expects($this->any())->method('getOption')->will($this->returnValueMap(
            array(
                array(self::TEST_OPTION_NAME, self::TEST_OPTION_VALUE),
                array('xxx', null),
            )
        ));

        \Magento\TestFramework\Helper\Cli::setOpt($this->_getOpt);
    }

    /**
     * Tesr down after test
     */
    public function tearDown()
    {
        $this->_getOpt = null;
        $this->_object = null;
    }

    /**
     * Test CLI helper
     */
    public function testCli()
    {
        $this->assertEquals(
            self::TEST_OPTION_VALUE,
            \Magento\TestFramework\Helper\Cli::getOption(self::TEST_OPTION_NAME)
        );
        $this->assertEquals(
            null,
            \Magento\TestFramework\Helper\Cli::getOption('xxx')
        );
        $this->assertEquals(
            'default',
            \Magento\TestFramework\Helper\Cli::getOption('xxx', 'default')
        );
    }
}
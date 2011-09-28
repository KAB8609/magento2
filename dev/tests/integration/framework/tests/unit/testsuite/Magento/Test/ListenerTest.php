<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_ListenerTestObserver
{
    private static $_calledMethods = array();

    /**
     * Collect method calls
     *
     * @param string $name
     * @param array $arguments
     * @throws Exception
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __call($name, $arguments)
    {
        self::$_calledMethods[] = array(get_class($this), $name);
    }

    /**
     * Reset collected method calls
     */
    public static function resetCalledMethods()
    {
        self::$_calledMethods = array();
    }

    /**
     * Assert that actually called methods equal to expected values.
     *
     * @param array $expectedMethods
     */
    public static function assertCalledMethods(array $expectedMethods)
    {
        PHPUnit_Framework_Assert::assertEquals($expectedMethods, self::$_calledMethods, 'Called observer methods.');
    }
}

class Magento_Test_ListenerTestObserverOne extends Magento_Test_ListenerTestObserver
{
}

class Magento_Test_ListenerTestObserverTwo extends Magento_Test_ListenerTestObserver
{
}

/**
 * Test class for Magento_Test_Listener.
 */
class Magento_Test_ListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Listener
     */
    protected $_listener;

    /**
     * Register observer classes within the listener
     */
    public static function setUpBeforeClass()
    {
        Magento_Test_Listener::registerObserver('Magento_Test_ListenerTestObserverOne');
        Magento_Test_Listener::registerObserver('Magento_Test_ListenerTestObserverTwo');
    }

    /**
     * Reset log of called methods
     */
    protected function setUp()
    {
        Magento_Test_ListenerTestObserver::resetCalledMethods();

        $this->_listener = new Magento_Test_Listener;
    }

    public function testGetCurrentTest()
    {
        $this->assertNull($this->_listener->getCurrentTest());
        $this->_listener->startTest($this);
        $this->assertSame($this, $this->_listener->getCurrentTest());
        $this->_listener->endTest($this, 0);
        $this->assertNull($this->_listener->getCurrentTest());
    }

    public function testAddError()
    {
        $this->_listener->addError($this, new Exception(), 0);
        Magento_Test_ListenerTestObserver::assertCalledMethods(array());
    }

    public function testAddFailure()
    {
        $this->_listener->addFailure($this, new PHPUnit_Framework_AssertionFailedError(), 0);
        Magento_Test_ListenerTestObserver::assertCalledMethods(array());
    }

    public function testAddIncompleteTest()
    {
        $this->_listener->addIncompleteTest($this, new Exception(), 0);
        Magento_Test_ListenerTestObserver::assertCalledMethods(array());
    }

    public function testAddSkippedTest()
    {
        $this->_listener->addSkippedTest($this, new Exception(), 0);
        Magento_Test_ListenerTestObserver::assertCalledMethods(array());
    }

    public function testStartTestSuite()
    {
        $this->_listener->startTestSuite(new PHPUnit_Framework_TestSuite);
        Magento_Test_ListenerTestObserver::assertCalledMethods(array(
            array('Magento_Test_ListenerTestObserverOne', 'startTestSuite'),
            array('Magento_Test_ListenerTestObserverTwo', 'startTestSuite'),
        ));
    }

    public function testEndTestSuite()
    {
        $this->_listener->endTestSuite(new PHPUnit_Framework_TestSuite);
        Magento_Test_ListenerTestObserver::assertCalledMethods(array(
            array('Magento_Test_ListenerTestObserverTwo', 'endTestSuite'),
            array('Magento_Test_ListenerTestObserverOne', 'endTestSuite'),
        ));
    }

    public function testStartTest()
    {
        $this->_listener->startTest($this);
        Magento_Test_ListenerTestObserver::assertCalledMethods(array(
            array('Magento_Test_ListenerTestObserverOne', 'startTest'),
            array('Magento_Test_ListenerTestObserverTwo', 'startTest'),
        ));
    }

    public function testEndTest()
    {
        $this->_listener->endTest($this, 0);
        Magento_Test_ListenerTestObserver::assertCalledMethods(array(
            array('Magento_Test_ListenerTestObserverTwo', 'endTest'),
            array('Magento_Test_ListenerTestObserverOne', 'endTest'),
        ));
    }
}

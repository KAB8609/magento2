<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code\Validator;

require_once(__DIR__ . '/_files/ClassesForContextAggregation.php');

class ContextAggregationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Code\Validator\ContextAggregation
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_fixturePath;

    protected function setUp()
    {
        $this->_model = new \Magento\Code\Validator\ContextAggregation();
        $this->_fixturePath  = realpath(__DIR__) . DIRECTORY_SEPARATOR
            . '_files' . DIRECTORY_SEPARATOR . 'ClassesForContextAggregation.php';
    }

    public function testClassArgumentAlreadyInjectedIntoContext()
    {
        $message = 'Incorrect dependency in class ClassArgumentAlreadyInjectedInContext in '
            . $this->_fixturePath . PHP_EOL . '\ClassFirst already exists in context object';

        $this->setExpectedException('\Magento\Code\ValidationException', $message);
        $this->_model->validate('ClassArgumentAlreadyInjectedInContext');
    }

    public function testClassArgumentWithInterfaceImplementation()
    {
        $this->assertTrue($this->_model->validate('ClassArgumentWithInterfaceImplementation'));
    }

    public function testClassArgumentWithInterface()
    {
        $this->assertTrue($this->_model->validate('ClassArgumentWithInterface'));
    }

    public function testClassArgumentWithAlreadyInjectedInterface()
    {
        $message = 'Incorrect dependency in class ClassArgumentWithAlreadyInjectedInterface in '
            . $this->_fixturePath . PHP_EOL . '\\InterfaceFirst already exists in context object';

        $this->setExpectedException('\Magento\Code\ValidationException', $message);
        $this->_model->validate('ClassArgumentWithAlreadyInjectedInterface');
    }
}
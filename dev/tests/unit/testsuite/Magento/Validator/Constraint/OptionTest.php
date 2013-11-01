<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Validator
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test case for \Magento\Validator\Constraint\Option
 */
namespace Magento\Validator\Constraint;

class OptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test getValue
     */
    public function testGetValue()
    {
        $expected = 'test_value';
        $option = new \Magento\Validator\Constraint\Option($expected);
        $this->assertEquals($expected, $option->getValue());
    }
}

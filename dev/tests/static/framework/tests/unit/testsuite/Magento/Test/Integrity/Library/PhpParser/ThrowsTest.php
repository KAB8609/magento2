<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Library\PhpParser;

use Magento\TestFramework\Integrity\Library\PhpParser\Throws;

/**
 * @package Magento\Test
 */
class ThrowsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Throws
     */
    protected $throws;

    /**
     * @var \Magento\TestFramework\Integrity\Library\PhpParser\Tokens|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $tokens;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->tokens = $this->getMockBuilder('Magento\TestFramework\Integrity\Library\PhpParser\Tokens')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Test get throws dependencies
     *
     * @test
     */
    public function testGetDependencies()
    {
        $tokens = array(
            0 => array(T_THROW, 'throw'),
            1 => array(T_WHITESPACE, ' '),
            2 => array(T_NEW, 'new'),
            3 => array(T_WHITESPACE, ' '),
            4 => array(T_NS_SEPARATOR, '\\'),
            5 => array(T_STRING, 'Exception'),
            6 => '(',
        );

        $this->tokens->expects($this->any())
            ->method('getTokenCodeByKey')
            ->will(
                $this->returnCallback(
                    function ($k) use ($tokens) {
                        return $tokens[$k][0];
                    }
                )
            );

        $this->tokens->expects($this->any())
            ->method('getTokenValueByKey')
            ->will(
                $this->returnCallback(
                    function ($k) use ($tokens) {
                        return $tokens[$k][1];
                    }
                )
            );

        $throws = new Throws($this->tokens);
        foreach ($tokens as $k => $token) {
            $throws->parse($token, $k);
        }

        $uses = $this->getMockBuilder('Magento\TestFramework\Integrity\Library\PhpParser\Uses')
            ->disableOriginalConstructor()
            ->getMock();

        $uses->expects($this->once())
            ->method('hasUses')
            ->will($this->returnValue(true));

        $uses->expects($this->once())
            ->method('getClassNameWithNamespace')
            ->will($this->returnValue('\Exception'));

        $this->assertEquals(
            array('\Exception'),
            $throws->getDependencies($uses)
        );
    }
}
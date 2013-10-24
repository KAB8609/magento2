<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\App\ResponseInterface
 */
namespace Magento\Core\Controller\Response;

class HttpTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test for getHeader method
     *
     * @dataProvider headersDataProvider
     * @covers \Magento\App\ResponseInterface::getHeader
     *
     * @param string $header
     */
    public function testGetHeaderExists($header)
    {
        $response = new \Magento\App\Response\Http();
        $response->headersSentThrowsException = false;
        $response->setHeader($header['name'], $header['value'], $header['replace']);
        $this->assertEquals($header, $response->getHeader($header['name']));
    }

    /**
     * Data provider for testGetHeader
     *
     * @return array
     */
    public function headersDataProvider()
    {
        return array(
            array(
                array(
                    'name' => 'X-Frame-Options',
                    'value' => 'SAMEORIGIN',
                    'replace' => true)
            ),
            array(
                array(
                    'name' => 'Test2',
                    'value' => 'Test2',
                    'replace' => false)
            )
        );
    }

    /**
     * Test for getHeader method. Validation for attempt to get not existing header
     *
     * @covers \Magento\App\ResponseInterface::getHeader
     *
     */
    public function testGetHeaderNotExists()
    {
        $response = new \Magento\App\Response\Http();
        $response->headersSentThrowsException = false;
        $response->setHeader('Name', 'value', true);
        $this->assertFalse($response->getHeader('Wrong name'));
    }
}

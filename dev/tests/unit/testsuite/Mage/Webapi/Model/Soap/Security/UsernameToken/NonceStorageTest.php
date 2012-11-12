<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test SOAP WS-Security UsernameToken nonce & timestamp storage implementation.
 */
class Mage_Webapi_Model_Soap_Security_UsernameToken_NonceStorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Soap_Security_UsernameToken_NonceStorage
     */
    protected $_nonceStorage;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * Set up cache instance mock and nonce storage object to be tested.
     */
    protected function setUp()
    {
        $this->_cacheMock = $this->getMockBuilder('Mage_Core_Model_Cache')
            ->disableOriginalConstructor()
            ->setMethods(array('load', 'save'))
            ->getMock();
        $this->_nonceStorage = new Mage_Webapi_Model_Soap_Security_UsernameToken_NonceStorage($this->_cacheMock);
    }

    /**
     * Clean up.
     */
    protected function tearDown()
    {
        unset($this->_cacheMock);
        unset($this->_nonceStorage);
    }

    /**
     * @param int $timestamp
     * @dataProvider invalidTimestampDataProvider
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_TimestampRefusedException
     */
    public function testValidateNonceInvalidTimestamp($timestamp)
    {
        $this->_nonceStorage->validateNonce('', $timestamp);
    }

    public static function invalidTimestampDataProvider()
    {
        return array(
            'Timestamp is zero' => array(0),
            'Timestamp is a string' => array('abcdef'),
            'Timestamp is negative' => array(-1),
            'Timestamp is too old' => array(
                time() - Mage_Webapi_Model_Soap_Security_UsernameToken_NonceStorage::NONCE_TTL
            ),
        );
    }

    public function testValidateNonceTimeStampFromFuture()
    {
        $this->setExpectedException('Mage_Webapi_Model_Soap_Security_UsernameToken_TimestampRefusedException');
        /** Timestamp is from future more far than 60 seconds must be prohibited */
        $this->_nonceStorage->validateNonce('', time() + 65);
    }

    public function testValidateNonce()
    {
        $nonce = 'abc123';
        $timestamp = time();

        $this->_cacheMock
            ->expects($this->once())
            ->method('load')
            ->with($this->_nonceStorage->getNonceCacheId($nonce))
            ->will($this->returnValue(false));
        $this->_cacheMock
            ->expects($this->once())
            ->method('save')
            ->with($timestamp, $this->_nonceStorage->getNonceCacheId($nonce),
            array(Mage_Webapi_Controller_Front_Soap::WEBSERVICE_CACHE_TAG),
            Mage_Webapi_Model_Soap_Security_UsernameToken_NonceStorage::NONCE_TTL
                + Mage_Webapi_Model_Soap_Security_UsernameToken_NonceStorage::NONCE_FROM_FUTURE_ACCEPTABLE_RANGE);

        $this->_nonceStorage->validateNonce($nonce, $timestamp);
    }

    /**
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_NonceUsedException
     */
    public function testValidateNonceUsed()
    {
        $nonce = 'abc123';
        $timestamp = time();

        $this->_cacheMock
            ->expects($this->once())
            ->method('load')
            ->with($this->_nonceStorage->getNonceCacheId($nonce))
            ->will($this->returnValue($timestamp));

        $this->_nonceStorage->validateNonce($nonce, $timestamp);
    }
}

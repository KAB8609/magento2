<?php
/**
 * Test SOAP server model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Soap_ServerTest extends PHPUnit_Framework_TestCase
{
    const WEBAPI_AREA_FRONT_NAME = 'webapi';

    /** @var Mage_Webapi_Model_Soap_Server */
    protected $_soapServer;

    /** @var Mage_Core_Model_App */
    protected $_appMock;

    /** @var Mage_Core_Model_Store */
    protected $_storeMock;

    /** @var Mage_Core_Model_Config */
    protected $_configMock;

    /** @var Mage_Webapi_Controller_Request_Soap */
    protected $_requestMock;

    /** @var Magento_DomDocument_Factory */
    protected $_domDocumentFactory;

    protected function setUp()
    {
        /** Init all dependencies for SUT. */
        $this->_storeMock = $this->getMockBuilder('Mage_Core_Model_Store')->disableOriginalConstructor()->getMock();
        $this->_storeMock->expects($this->any())->method('getBaseUrl')->will(
            $this->returnValue('http://magento.com/')
        );

        $this->_configMock = $this->getMockBuilder('Mage_Core_Model_Config')->disableOriginalConstructor()->getMock();
        $this->_configMock->expects($this->any())->method('getAreaFrontName')->will(
            $this->returnValue(self::WEBAPI_AREA_FRONT_NAME)
        );

        $this->_appMock = $this->getMockBuilder('Mage_Core_Model_App')->disableOriginalConstructor()->getMock();
        $this->_appMock->expects($this->any())->method('getStore')->will($this->returnValue($this->_storeMock));
        $this->_appMock->expects($this->any())->method('getConfig')->will($this->returnValue($this->_configMock));

        $this->_requestMock = $this->getMockBuilder('Mage_Webapi_Controller_Request_Soap')->disableOriginalConstructor()
            ->getMock();
        $reqRes = array(
            'catalogProduct' => 'V1'
        );
        $this->_requestMock->expects($this->any())->method('getParam')
            ->will($this->returnValue($reqRes));
        $this->_domDocumentFactory = $this->getMockBuilder('Magento_DomDocument_Factory')
            ->disableOriginalConstructor()->getMock();

        /** Init SUT. */
        $this->_soapServer = new Mage_Webapi_Model_Soap_Server(
            $this->_appMock,
            $this->_requestMock,
            $this->_domDocumentFactory
        );

        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_soapServer);
        unset($this->_appMock);
        unset($this->_requestMock);
        unset($this->_storeMock);
        parent::tearDown();
    }

    /**
     * Test getApiCharset method.
     */
    public function testGetApiCharset()
    {
        $this->_storeMock->expects($this->once())->method('getConfig')->will($this->returnValue('Windows-1251'));
        $this->assertEquals(
            'Windows-1251',
            $this->_soapServer->getApiCharset(),
            'API charset encoding getting is invalid.'
        );
    }

    /**
     * Test getApiCharset method with default encoding.
     */
    public function testGetApiCharsetDefaultEncoding()
    {
        $this->_storeMock->expects($this->once())->method('getConfig')->will($this->returnValue(null));
        $this->assertEquals(
            Mage_Webapi_Model_Soap_Server::SOAP_DEFAULT_ENCODING,
            $this->_soapServer->getApiCharset(),
            'Default API charset encoding getting is invalid.'
        );
    }

    /**
     * Test getEndpointUri method.
     */
    public function testGetEndpointUri()
    {
        $expectedResult = 'http://magento.com/' . self::WEBAPI_AREA_FRONT_NAME . '/'
            . Mage_Webapi_Controller_Front::API_TYPE_SOAP;
        $actualResult = $this->_soapServer->getEndpointUri();
        $this->assertEquals($expectedResult, $actualResult, 'Endpoint URI building is invalid.');
    }

    /**
     * Test fault method with Exception.
     */
    public function testExceptionFault()
    {
        /** Init Exception. */
        $exception = new Exception();
        $faultResult = $this->_soapServer->fault($exception);
        /** Assert that returned object is instance of SoapFault class. */
        $this->assertInstanceOf('SoapFault', $faultResult, 'SoapFault was not returned.');
    }

    /**
     * Test fault method with Mage_Webapi_Model_Soap_Fault.
     */
    public function testWebapiSoapFault()
    {
        /** Mock Webapi Soap fault. */
        $apiFault = $this->getMockBuilder('Mage_Webapi_Model_Soap_Fault')->disableOriginalConstructor()->getMock();
        /** Assert that mocked fault toXml method will be executed once. */
        $apiFault->expects($this->once())->method('toXml');
        $this->_soapServer->fault($apiFault);
    }


    /**
     * Test generate uri with wsdl param as true
     */
    public function testGenerateUriWithWsdlParam()
    {
        $expectedResult = 'http://magento.com/webapi/soap?services[catalogProduct]=V1&wsdl=1';
        $actualResult = $this->_soapServer->generateUri(true);
        $this->assertEquals($expectedResult, urldecode($actualResult), 'URI (with WSDL param) generated is invalid.');
    }

    /**
     * Test generate uri with wsdl param as true
     */
    public function testGenerateUriWithNoWsdlParam()
    {
        $expectedResult = 'http://magento.com/webapi/soap?services[catalogProduct]=V1';
        $actualResult = $this->_soapServer->generateUri(false);
        $this->assertEquals(
            $expectedResult,
            urldecode($actualResult),
            'URI (without WSDL param) generated is invalid.'
        );
    }

}

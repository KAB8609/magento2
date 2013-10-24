<?php
/**
 * Test SOAP fault model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Soap;

class FaultTest extends \PHPUnit_Framework_TestCase
{
    protected $_wsdlUrl = 'http://host.com/?wsdl&services=customerV1';

    /** @var \Magento\Core\Model\App */
    protected $_appMock;

    /** @var \Magento\Webapi\Model\Soap\Server */
    protected $_soapServerMock;

    /** @var \Magento\Webapi\Model\Soap\Fault */
    protected $_soapFault;

    protected function setUp()
    {
        $this->_appMock = $this->getMockBuilder('Magento\Core\Model\App')->disableOriginalConstructor()->getMock();
        $localeMock = $this->getMockBuilder('Magento\Core\Model\LocaleInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $localeMock->expects($this->any())->method('getLocale')->will($this->returnValue(new \Zend_Locale('en_US')));
        $this->_appMock->expects($this->any())->method('getLocale')->will($this->returnValue($localeMock));
        /** Initialize SUT. */
        $message = "Soap fault reason.";
        $details = array('param1' => 'value1', 'param2' => 2);
        $code = 111;
        $webapiException = new \Magento\Webapi\Exception(
            $message,
            $code,
            \Magento\Webapi\Exception::HTTP_INTERNAL_ERROR,
            $details
        );
        $this->_soapServerMock = $this->getMockBuilder('Magento\Webapi\Model\Soap\Server')->disableOriginalConstructor()
            ->getMock();
        $this->_soapServerMock->expects($this->any())->method('generateUri')->will($this->returnValue($this->_wsdlUrl));

        $this->_soapFault = new \Magento\Webapi\Model\Soap\Fault(
            $this->_appMock,
            $this->_soapServerMock,
            $webapiException
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_soapFault);
        unset($this->_appMock);
        parent::tearDown();
    }

    public function testToXmlDeveloperModeOff()
    {
        $this->_appMock->expects($this->any())->method('isDeveloperMode')->will($this->returnValue(false));
        $wsdlUrl = urlencode($this->_wsdlUrl);
        $expectedResult = <<<XML
<?xml version="1.0" encoding="utf-8" ?>
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" xmlns:m="{$wsdlUrl}">
    <env:Body>
        <env:Fault>
            <env:Code>
                <env:Value>env:Receiver</env:Value>
            </env:Code>
            <env:Reason>
                <env:Text xml:lang="en">Soap fault reason.</env:Text>
            </env:Reason>
            <env:Detail>
                <m:DefaultFault>
                    <m:Parameters>
                        <m:param1>value1</m:param1>
                        <m:param2>2</m:param2>
                    </m:Parameters>
                    <m:Code>111</m:Code>
                </m:DefaultFault>
            </env:Detail>
        </env:Fault>
    </env:Body>
</env:Envelope>
XML;
        $actualXml = $this->_soapFault->toXml();
        $this->assertXmlStringEqualsXmlString(
            $expectedResult,
            $actualXml,
            'Wrong SOAP fault message with default parameters.'
        );
    }

    public function testToXmlDeveloperModeOn()
    {
        $this->_appMock->expects($this->any())->method('isDeveloperMode')->will($this->returnValue(true));
        $actualXml = $this->_soapFault->toXml(true);
        $this->assertContains('<m:Trace>', $actualXml, 'Exception trace is not found in XML.');
    }

    /**
     * Test getSoapFaultMessage method.
     *
     * @dataProvider dataProviderForGetSoapFaultMessageTest
     */
    public function testGetSoapFaultMessage(
        $faultReason,
        $faultCode,
        $additionalParameters,
        $expectedResult,
        $assertMessage
    ) {
        $actualResult = $this->_soapFault->getSoapFaultMessage(
            $faultReason,
            $faultCode,
            $additionalParameters
        );
        $this->assertXmlStringEqualsXmlString($expectedResult, $actualResult, $assertMessage);
    }

    /**
     * Data provider for GetSoapFaultMessage test.
     *
     * @return array
     */
    public function dataProviderForGetSoapFaultMessageTest()
    {
        /** Include file with all expected SOAP fault XMLs. */
        $expectedXmls = include __DIR__ . '/../../_files/soap_fault/soap_fault_expected_xmls.php';
        return array(
            //Each array contains data for SOAP Fault Message, Expected XML, and Assert Message.
            array(
                'Fault reason',
                'Sender',
                array('key1' => 'value1', 'key2' => 'value2'),
                $expectedXmls['expectedResultArrayDataDetails'],
                'SOAP fault message with associated array data details is invalid.'
            ),
            array(
                'Fault reason',
                'Sender',
                array('value1', 'value2'),
                $expectedXmls['expectedResultIndexArrayDetails'],
                'SOAP fault message with index array data details is invalid.'
            ),
            array(
                'Fault reason',
                'Sender',
                array(),
                $expectedXmls['expectedResultEmptyArrayDetails'],
                'SOAP fault message with empty array data details is invalid.'
            ),
            array(
                'Fault reason',
                'Sender',
                (object)array('key' => 'value'),
                $expectedXmls['expectedResultObjectDetails'],
                'SOAP fault message with object data details is invalid.'
            ),
            array(
                'Fault reason',
                'Sender',
                array('key' => array('sub_key' => 'value')),
                $expectedXmls['expectedResultComplexDataDetails'],
                'SOAP fault message with complex data details is invalid.'
            ),
        );
    }

    public function testConstructor()
    {
        $message = "Soap fault reason.";
        $details = array('param1' => 'value1', 'param2' => 2);
        $code = 111;
        $webapiException = new \Magento\Webapi\Exception(
            $message,
            $code,
            \Magento\Webapi\Exception::HTTP_INTERNAL_ERROR,
            $details
        );
        $soapFault = new \Magento\Webapi\Model\Soap\Fault(
            $this->_appMock,
            $this->_soapServerMock,
            $webapiException
        );
        $actualXml = $soapFault->toXml();
        $wsdlUrl = urlencode($this->_wsdlUrl);
        $expectedXml = <<<FAULT_XML
<?xml version="1.0" encoding="utf-8" ?>
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" xmlns:m="{$wsdlUrl}">
    <env:Body>
        <env:Fault>
            <env:Code>
                <env:Value>env:Receiver</env:Value>
            </env:Code>
            <env:Reason>
                <env:Text xml:lang="en">{$message}</env:Text>
            </env:Reason>
            <env:Detail>
                <m:ErrorDetails>
                    <m:Parameters>
                        <m:param1>value1</m:param1>
                        <m:param2>2</m:param2>
                    </m:Parameters>
                    <m:Code>{$code}</m:Code>
                </m:ErrorDetails>
            </env:Detail>
        </env:Fault>
    </env:Body>
</env:Envelope>
FAULT_XML;
        $this->assertXmlStringEqualsXmlString($expectedXml, $actualXml, "Soap fault is invalid.");
    }
}

<?php
/**
 * Test \Magento\Webapi\Model\Soap\Wsdl\Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Soap_Wsdl_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManagerMock;

    /** @var \Magento\Webapi\Model\Soap\Wsdl\Factory */
    protected $_soapWsdlFactory;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMockForAbstractClass();
        $this->_soapWsdlFactory = new \Magento\Webapi\Model\Soap\Wsdl\Factory($this->_objectManagerMock);
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_objectManagerMock);
        unset($this->_soapWsdlFactory);
        parent::tearDown();
    }

    public function testCreate()
    {
        $wsdlName = 'wsdlName';
        $endpointUrl = 'endpointUrl';
        $this->_objectManagerMock
            ->expects($this->once())
            ->method('create')
            ->with('Magento\Webapi\Model\Soap\Wsdl', array('name' => $wsdlName, 'uri' => $endpointUrl));
        $this->_soapWsdlFactory->create($wsdlName, $endpointUrl);
    }
}

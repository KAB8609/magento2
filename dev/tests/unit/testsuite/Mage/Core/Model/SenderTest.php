<?php
/**
 * Unit test for customer service layer Mage_Core_Model_Sender
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Sender
 */
class Mage_Core_Model_SenderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Sender
     */
    protected $_model;

    /** @var Mage_Core_Model_Email_Template_Mailer|PHPUnit_Framework_MockObject_MockObject */
    protected $_mailerMock;

    /** @var Mage_Core_Model_Store|PHPUnit_Framework_MockObject_MockObject */
    protected $_storeMock;

    /** @var Mage_Core_Model_Email_Info|PHPUnit_Framework_MockObject_MockObject */
    protected $_emailInfoMock;

    /**
     * Set required values
     */
    public function setUp()
    {
        $this->_mailerMock = $this->getMockBuilder('Mage_Core_Model_Email_Template_Mailer')
            ->disableOriginalConstructor()
            ->setMethods(array('addEmailInfo', 'setSender', 'setStoreId', 'setTemplateId', 'setTemplateParams', 'send'))
            ->getMock();
        $this->_storeMock = $this->getMockBuilder('Mage_Core_Model_Store')
            ->disableOriginalConstructor()
            ->setMethods(array('load', 'getConfig'))
            ->getMock();
        $this->_emailInfoMock = $this->getMockBuilder('Mage_Core_Model_Email_Info')
            ->disableOriginalConstructor()
            ->setMethods(array('addTo'))
            ->getMock();

        $this->_model = new Mage_Core_Model_Sender($this->_mailerMock, $this->_emailInfoMock, $this->_storeMock);
    }

    public function testSend()
    {
        $email = 'test@example.com';
        $name = 'test';
        $template = 'letter_template_xml_path';
        $sender = 'sender_template_xml_path';
        $params = array('param1');
        $storeId = 1;

        $this->_storeMock->expects($this->once())->method('load')->with($this->equalTo($storeId));
        $this->_storeMock->setStoreId($storeId);

        $this->_storeMock->expects($this->at(1))
            ->method('getConfig')
            ->with($this->equalTo($sender), $this->equalTo($storeId))
            ->will($this->returnValue($sender)
        );
        $this->_storeMock->expects($this->at(2))
            ->method('getConfig')
            ->with($this->equalTo($template), $this->equalTo($storeId))
            ->will($this->returnValue($template)
        );

        $this->_mailerMock->expects($this->once())->method('addEmailInfo')->with($this->equalTo($this->_emailInfoMock));
        $this->_mailerMock->expects($this->once())->method('setSender')->with($this->equalTo($sender));
        $this->_mailerMock->expects($this->once())->method('setStoreId')->with($this->equalTo($storeId));
        $this->_mailerMock->expects($this->once())->method('setTemplateId')->with($this->equalTo($template));
        $this->_mailerMock->expects($this->once())->method('setTemplateParams')->with($this->equalTo($params));
        $this->_mailerMock->expects($this->once())->method('send');

        $this->_emailInfoMock->expects($this->once())->method('addTo')->with($this->equalTo($email), $this->equalTo($name));

        $this->_model->send($email, $name, $template, $sender, $params, $storeId);
    }
}

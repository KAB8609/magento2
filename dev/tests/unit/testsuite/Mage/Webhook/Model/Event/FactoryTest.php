<?php
/**
 * Mage_Webhook_Model_Event_Factory
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Event_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webhook_Model_Event_Factory */
    protected $_factory;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManager;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_arrayConverter;

    public function setUp()
    {
        $this->_objectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_arrayConverter = $this->getMockBuilder('Magento_Convert_Object')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_factory = new Mage_Webhook_Model_Event_Factory($this->_objectManager, $this->_arrayConverter);
    }

    public function testCreate()
    {
        $webhookEvent = $this->getMockBuilder('Mage_Webhook_Model_Event')
            ->disableOriginalConstructor()
            ->getMock();
        $topic = 'TEST_TOPIC';
        $data = 'TEST_DATA';
        $array = 'TEST_ARRAY';
        $this->_arrayConverter->expects($this->once())
            ->method('convertDataToArray')
            ->with($this->equalTo($data))
            ->will($this->returnValue($array));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('Mage_Webhook_Model_Event'),
                $this->equalTo(
                    array(
                         'data' => array(
                             'topic'     => $topic,
                             'body_data' => serialize($array)
                         )
                    )
                )
            )
            ->will($this->returnValue($webhookEvent));
        $webhookEvent->expects($this->once())
            ->method('setDataChanges')
            ->with($this->equalTo(true))
            ->will($this->returnSelf());
        $this->assertSame($webhookEvent, $this->_factory->create($topic, $data));
    }

    public function testCreateEmpty()
    {
        $testValue = "test value";
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Mage_Webhook_Model_Event'))
            ->will($this->returnValue($testValue));
        $this->assertSame($testValue, $this->_factory->createEmpty());
    }
}

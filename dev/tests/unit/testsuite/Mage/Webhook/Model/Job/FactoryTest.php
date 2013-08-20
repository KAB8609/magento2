<?php
/**
 * Mage_Webhook_Model_Job_Factory
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Job_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webhook_Model_Job_Factory */
    private $_jobFactory;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_mockObjectManager;

    public function setUp()
    {
        $this->_mockObjectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_jobFactory = new Mage_Webhook_Model_Job_Factory($this->_mockObjectManager);
    }

    public function testCreate()
    {
        $subscription = $this->getMockBuilder('Magento_PubSub_SubscriptionInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $event = $this->getMockBuilder('Magento_PubSub_EventInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $job = 'JOB';
        $this->_mockObjectManager->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('Mage_Webhook_Model_Job'),
                $this->equalTo(
                    array(
                         'data' => array(
                             'event'        => $event,
                             'subscription' => $subscription
                         )
                    )
                )
            )
            ->will($this->returnValue($job));
        $this->assertSame($job, $this->_jobFactory->create($subscription, $event));
    }
}
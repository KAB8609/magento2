<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftRegistry
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Enterprise_GiftRegistry_Model_Entity
 */
class Enterprise_GiftRegistry_Model_EntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * GiftRegistry instance
     *
     * @var Enterprise_GiftRegistry_Model_Entity
     */
    protected $_model;

    /**
     * Mock for store instance
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Mock from email template instance
     *
     * @var Mage_Core_Model_Email_Template
     */
    protected $_emailTemplate;

    public function setUp()
    {
        $app = $this->getMock('Mage_Core_Model_App', array(), array(), '', false);
        $resource = $this->getMock('Enterprise_GiftRegistry_Model_Resource_Entity', array(), array(), '', false);
        $helper = $this->getMock('Enterprise_GiftRegistry_Helper_Data',
            array('__', 'getRegistryLink'), array(), '', false, false
        );
        $design = $this->getMock('Mage_Core_Model_Design_Package', array(), array(), '', false, false);
        $loader = $this->getMock('Mage_Core_Model_Locale_Hierarchy_Loader', array(), array(), '', false, false);
        $translateFactory = $this->getMock('Mage_Core_Model_Translate_Factory',
            array(), array(), '', false, false);
        $objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false, false);
        $loader->expects($this->any())->method('load')->will($this->returnValue(array()));
        $translate = $this->getMock('Mage_Core_Model_Translate', array(),
            array($design, $loader, $translateFactory), '', true, false);

        $config = $this->getMock('Mage_Core_Model_Config', array('getModelInstance'), array(), '', false);
        $this->_store = $this->getMock('Mage_Core_Model_Store', array(), array(), '', false);
        $this->_emailTemplate = $this->getMock('Mage_Core_Model_Email_Template',
            array('setDesignConfig', 'sendTransactional'), array(), '', false
        );

        $app->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->_store));

        $this->_store->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        $emailTemplate = $this->_emailTemplate;

        $config->expects($this->any())
            ->method('getModelInstance')
            ->with($this->equalTo('Mage_Core_Model_Email_Template'))
            ->will($this->returnCallback(
                function () use ($emailTemplate) {
                    return clone $emailTemplate;
                }
            ));

        $eventDispatcher = $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false, false);
        $cacheManager = $this->getMock('Mage_Core_Model_CacheInterface', array(), array(), '', false, false);
        $context = new Mage_Core_Model_Context($eventDispatcher, $cacheManager);

        $this->_model = new Enterprise_GiftRegistry_Model_Entity(
            $context, $app, $this->_store, $config, $translate, $resource, null, array(
                'helpers' => array('Enterprise_GiftRegistry_Helper_Data' => $helper)
            )
        );
    }

    /**
     * @param array $arguments
     * @param array $expectedResult
     * @dataProvider invalidSenderAndRecipientInfoDataProvider
     */
    public function testSendShareRegistryEmailsWithInvalidSenderAndRecipientInfoReturnsError($arguments,
        $expectedResult
    ) {
        $this->_initSenderInfo($arguments['sender_name'], $arguments['sender_message'], $arguments['sender_email']);
        $this->_model->setRecipients($arguments['recipients']);
        $result = $this->_model->sendShareRegistryEmails();

        $this->assertEquals($expectedResult['success'], $result->getIsSuccess());
        $this->assertEquals($expectedResult['error_message'], $result->getErrorMessage());
    }

    public function testSendShareRegistryEmailsWithValidDataReturnsSuccess()
    {
        $this->_initSenderInfo('John Doe', 'Hello world', 'john.doe@example.com');
        $this->_model->setRecipients(array(array(
            'email' => 'john.doe@example.com',
            'name' => 'John Doe'
        )));
        $this->_emailTemplate->setSentSuccess(true);
        $result = $this->_model->sendShareRegistryEmails();

        $this->assertTrue($result->getIsSuccess());
        $this->assertTrue($result->hasSuccessMessage());
    }

    public function testSendShareRegistryEmailsWithErrorInMailerReturnsError()
    {
        $this->_initSenderInfo('John Doe', 'Hello world', 'john.doe@example.com');
        $this->_model->setRecipients(array(array(
            'email' => 'john.doe@example.com',
            'name' => 'John Doe'
        )));
        $this->_emailTemplate->setSentSuccess(false);
        $result = $this->_model->sendShareRegistryEmails();

        $this->assertTrue($result->hasErrorMessage());
        $this->assertContains('Failed to share gift registry.', $result->getErrorMessage());
    }

    public function invalidSenderAndRecipientInfoDataProvider()
    {
        return array_merge(
            $this->_invalidRecipientInfoDataProvider(),
            $this->_invalidSenderInfoDataProvider()
        );
    }

    /**
     * Retrieve data for invalid sender cases
     *
     * @return array
     */
    protected function _invalidSenderInfoDataProvider()
    {
        return array(
            array(
                array(
                    'sender_name' => null,
                    'sender_message' => 'Hello world',
                    'sender_email' => 'email',
                    'recipients' => array()
                ),
                array(
                    'success' => false,
                    'error_message' => 'Sender data can\'t be empty.'
                )
            ),
            array(
                array(
                    'sender_name' => 'John Doe',
                    'sender_message' => null,
                    'sender_email' => 'email',
                    'recipients' => array()
                ),
                array(
                    'success' => false,
                    'error_message' => 'Sender data can\'t be empty.'
                )
            ),
            array(
                array(
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => null,
                    'recipients' => array()
                ),
                array(
                    'success' => false,
                    'error_message' => 'Sender data can\'t be empty.'
                )
            ),
            array(
                array(
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => 'invalid_email',
                    'recipients' => array()
                ),
                array(
                    'success' => false,
                    'error_message' => 'Please input a valid sender email address.'
                )
            )
        );
    }

    /**
     * Retrieve data for invalid recipient cases
     *
     * @return array
     */
    protected function _invalidRecipientInfoDataProvider()
    {
        return array(
            array(
                array(
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => 'john.doe@example.com',
                    'recipients' => array(array(
                        'email' => 'invalid_email'
                    ))
                ),
                array(
                    'success' => false,
                    'error_message' => 'Please input a valid recipient email address.'
                )
            ),
            array(
                array(
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => 'john.doe@example.com',
                    'recipients' => array(array(
                        'email' => 'john.doe@example.com',
                        'name' => ''
                    ))
                ),
                array(
                    'success' => false,
                    'error_message' => 'Please input a recipient name.'
                )
            ),
            array(
                array(
                    'sender_name' => 'John Doe',
                    'sender_message' => 'Hello world',
                    'sender_email' => 'john.doe@example.com',
                    'recipients' => array()
                ),
                array(
                    'success' => false,
                    'error_message' => null
                )
            )
        );
    }

    /**
     * Initialize sender info
     *
     * @param string $senderName
     * @param string $senderMessage
     * @param string $senderEmail
     * @return void
     */
    protected function _initSenderInfo($senderName, $senderMessage, $senderEmail)
    {
        $this->_model
            ->setSenderName($senderName)
            ->setSenderMessage($senderMessage)
            ->setSenderEmail($senderEmail);
    }
}

<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_AdminNotification_Model_System_MessageList
{
    /**
     * List of configured message classes
     *
     * @var array
     */
    protected $_messageClasses;

    /**
     * List of messages
     *
     * @var array
     */
    protected $_messages;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param array $messages
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        $messages = array()
    ) {
        $this->_objectManager = $objectManager;
        $this->_messageClasses = $messages;
    }

    /**
     * Load messages to display
     *
     * @throws InvalidArgumentException
     */
    protected function _loadMessages()
    {
        if (!$this->_messages) {
            foreach ($this->_messageClasses as $key => $messageClass) {
                if (!$messageClass) {
                    throw new InvalidArgumentException('Message class for message "' . $key . '" is not set');
                }
                $message = $this->_objectManager->get($messageClass);
                $this->_messages[$message->getIdentity()] = $message;
            }
        }
    }

    /**
     * Retrieve message by
     *
     * @param string $identity
     * @return null|Mage_AdminNotification_Model_System_MessageInterface
     */
    public function getMessageByIdentity($identity)
    {
        $this->_loadMessages();
        return isset($this->_messages[$identity]) ? $this->_messages[$identity] : null;
    }

    /**
     * Retrieve list of all messages
     *
     * @return Mage_AdminNotification_Model_System_MessageInterface[]
     */
    public function asArray()
    {
        $this->_loadMessages();
        return $this->_messages;
    }
}

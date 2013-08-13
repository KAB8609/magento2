<?php
/**
 * Creates a block given failed registration
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Block_Adminhtml_Registration_Failed extends Magento_Backend_Block_Template
{
    /** @var  Magento_Backend_Model_Session */
    protected $_session;

    /**
     * @param Magento_Backend_Model_Session $session
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Model_Session $session,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_session = $session;
    }

    /**
     * Get error message produced on failure
     *
     * @return string The error message produced upon failure
     */
    public function getSessionError()
    {
        return $this->_session->getMessages(true)
            ->getLastAddedMessage()
            ->toString();
    }
}

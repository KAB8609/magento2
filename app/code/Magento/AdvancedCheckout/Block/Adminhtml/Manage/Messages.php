<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin Checkout block for showing messages
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage;

class Messages extends \Magento\Core\Block\Messages
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Message $message
     * @param \Magento\Core\Model\Message\CollectionFactory $messageFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Message $message,
        \Magento\Core\Model\Message\CollectionFactory $messageFactory,
        array $data = array()
    ) {
        $this->_backendSession = $backendSession;
        parent::__construct($coreData, $context, $message, $messageFactory, $data);
    }

    /**
     * Prepares layout for current block
     */
    protected function _prepareLayout()
    {
        $this->addMessages($this->_backendSession->getMessages(true));
        parent::_prepareLayout();
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create gift message form
 *
 * @category   Magento
 * @package    Magento_GiftMessage
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftMessage\Block\Adminhtml\Sales\Order\Create;

class Form extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Adminhtml\Model\Session\Quote
     */
    protected $_sessionQuote;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Adminhtml\Model\Session\Quote $sessionQuote
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Adminhtml\Model\Session\Quote $sessionQuote,
        array $data = array()
    ) {
        $this->_sessionQuote = $sessionQuote;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Indicates that block can display gift message form
     *
     * @return bool
     */
    public function canDisplayGiftmessageForm()
    {
        $quote = $this->_sessionQuote->getQuote();
        return $this->helper('Magento\GiftMessage\Helper\Message')->getIsMessagesAvailable('items', $quote, $quote->getStore());
    }
}

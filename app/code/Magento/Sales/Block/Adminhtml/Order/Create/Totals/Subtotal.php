<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Subtotal Total Row Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create\Totals;

class Subtotal extends \Magento\Sales\Block\Adminhtml\Order\Create\Totals\DefaultTotals
{
    protected $_template = 'sales/order/create/totals/subtotal.phtml';

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxConfig;

    /**
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Sales\Helper\Data $salesData
     * @param \Magento\Adminhtml\Model\Session\Quote $sessionQuote
     * @param \Magento\Adminhtml\Model\Sales\Order\Create $orderCreate
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Sales\Helper\Data $salesData,
        \Magento\Adminhtml\Model\Session\Quote $sessionQuote,
        \Magento\Adminhtml\Model\Sales\Order\Create $orderCreate,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Model\Config $salesConfig,
        array $data = array()
    ) {
        $this->_taxConfig = $taxConfig;
        parent::__construct($salesData, $sessionQuote, $orderCreate, $coreData, $context, $salesConfig, $data);
    }

    /**
     * Check if we need display both sobtotals
     *
     * @return bool
     */
    public function displayBoth()
    {
        /**
         * Check without store parameter - we wil get admin configuration value
         */
        return $this->_taxConfig->displayCartSubtotalBoth();
    }
}

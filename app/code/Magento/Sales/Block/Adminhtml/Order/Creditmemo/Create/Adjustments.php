<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Order\Creditmemo\Create;

class Adjustments extends \Magento\Adminhtml\Block\Template
{
    protected $_source;

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Tax\Model\Config $taxConfig,
        array $data = array()
    ) {
        $this->_taxConfig = $taxConfig;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Initialize creditmemo agjustment totals
     *
     * @return \Magento\Tax\Block\Sales\Order\Tax
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_source  = $parent->getSource();
        $total = new \Magento\Object(array(
            'code'      => 'agjustments',
            'block_name'=> $this->getNameInLayout()
        ));
        $parent->removeTotal('shipping');
        $parent->removeTotal('adjustment_positive');
        $parent->removeTotal('adjustment_negative');
        $parent->addTotal($total);
        return $this;
    }

    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Get credit memo shipping amount depend on configuration settings
     * @return float
     */
    public function getShippingAmount()
    {
        $source = $this->getSource();
        if ($this->_taxConfig->displaySalesShippingInclTax($source->getOrder()->getStoreId())) {
            $shipping = $source->getBaseShippingInclTax();
        } else {
            $shipping = $source->getBaseShippingAmount();
        }
        return $this->_storeManager->getStore()->roundPrice($shipping) * 1;
    }

    /**
     * Get label for shipping total based on configuration settings
     * @return string
     */
    public function getShippingLabel()
    {
        $source = $this->getSource();
        if ($this->_taxConfig->displaySalesShippingInclTax($source->getOrder()->getStoreId())) {
            $label = __('Refund Shipping (Incl. Tax)');
        } elseif ($this->_taxConfig->displaySalesShippingBoth($source->getOrder()->getStoreId())) {
            $label = __('Refund Shipping (Excl. Tax)');
        } else {
            $label = __('Refund Shipping');
        }
        return $label;
    }
}

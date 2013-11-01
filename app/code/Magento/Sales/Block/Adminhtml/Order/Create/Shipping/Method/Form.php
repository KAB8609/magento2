<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create shipping method form block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method;

class Form
    extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    protected $_rates;

    /**
     * Tax data
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData = null;

    /**
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Adminhtml\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Adminhtml\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_taxData = $taxData;
        parent::__construct($sessionQuote, $orderCreate, $coreData, $context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_shipping_method_form');
    }

    /**
     * Retrieve quote shipping address model
     *
     * @return \Magento\Sales\Model\Quote\Address
     */
    public function getAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }

    /**
     * Retrieve array of shipping rates groups
     *
     * @return array
     */
    public function getShippingRates()
    {
        if (empty($this->_rates)) {
            $this->_rates = $this->getAddress()->getGroupedAllShippingRates();
        }
        return $this->_rates;
    }

    /**
     * Rertrieve carrier name from store configuration
     *
     * @param   string $carrierCode
     * @return  string
     */
    public function getCarrierName($carrierCode)
    {
        if ($name = $this->_storeConfig->getConfig('carriers/'.$carrierCode.'/title', $this->getStore()->getId())) {
            return $name;
        }
        return $carrierCode;
    }

    /**
     * Retrieve current selected shipping method
     *
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }

    /**
     * Check activity of method by code
     *
     * @param   string $code
     * @return  bool
     */
    public function isMethodActive($code)
    {
        return $code===$this->getShippingMethod();
    }

    /**
     * Retrieve rate of active shipping method
     *
     * @return \Magento\Sales\Model\Quote\Address\Rate || false
     */
    public function getActiveMethodRate()
    {
        $rates = $this->getShippingRates();
        if (is_array($rates)) {
            foreach ($rates as $group) {
                foreach ($group as $rate) {
                    if ($rate->getCode() == $this->getShippingMethod()) {
                        return $rate;
                    }
                }
            }
        }
        return false;
    }

    public function getIsRateRequest()
    {
        return $this->getRequest()->getParam('collect_shipping_rates');
    }

    public function getShippingPrice($price, $flag)
    {
        return $this->getQuote()->getStore()->convertPrice(
            $this->_taxData->getShippingPrice(
                $price,
                $flag,
                $this->getAddress(),
                null,
                //We should send exact quote store to prevent fetching default config for admin store.
                $this->getAddress()->getQuote()->getStore()
            ),
            true
        );
    }
}

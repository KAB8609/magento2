<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal online logo with additional options
 */
namespace Magento\Paypal\Block;

class Logo extends \Magento\Core\Block\Template
{
    /**
     * @var \Magento\Paypal\Model\Config
     */
    protected $_paypalConfig;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Paypal\Model\Config $paypalConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Paypal\Model\Config $paypalConfig,
        array $data = array()
    ) {
        $this->_paypalConfig = $paypalConfig;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Return URL for Paypal Landing page
     *
     * @return string
     */
    public function getAboutPaypalPageUrl()
    {
        return $this->_getConfig()->getPaymentMarkWhatIsPaypalUrl($this->_locale);
    }

    /**
     * Getter for paypal config
     *
     * @return \Magento\Paypal\Model\Config
     */
    protected function _getConfig()
    {
        return $this->_paypalConfig;
    }

    /**
     * Disable block output if logo turned off
     *M
     * @return string
     */
    protected function _toHtml()
    {
        $type = $this->getLogoType(); // assigned in layout etc.
        $logoUrl = $this->_getConfig()->getAdditionalOptionsLogoUrl($this->_locale->getLocaleCode(), $type);
        if (!$logoUrl) {
            return '';
        }
        $this->setLogoImageUrl($logoUrl);
        return parent::_toHtml();
    }
}

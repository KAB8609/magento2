<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Block\Checkout\Cart\Item;

class Renderer extends \Magento\Checkout\Block\Cart\Item\Renderer
{
    /**
     * Gift card catalog product configuration
     *
     * @var \Magento\GiftCard\Helper\Catalog\Product\Configuration
     */
    protected $_giftCardCtlgProdConfigur = null;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\GiftCard\Helper\Catalog\Product\Configuration $giftCardCtlgProdConfigur
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Catalog\Helper\Product\Configuration $productConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\GiftCard\Helper\Catalog\Product\Configuration $giftCardCtlgProdConfigur,
        array $data = array()
    ) {
        $this->_giftCardCtlgProdConfigur = $giftCardCtlgProdConfigur;
        parent::__construct($context, $coreData, $productConfig, $checkoutSession, $data);
    }

    /**
     * Prepare custom option for display, returns false if there's no value
     *
     * @param string $code
     * @return mixed
     */
    protected function _prepareCustomOption($code)
    {
        return $this->_giftCardCtlgProdConfigur
            ->prepareCustomOption($this->getItem(), $code);
    }

    /**
     * Get gift card option list
     *
     * @return array
     */
    protected function _getGiftcardOptions()
    {
        return $this->_giftCardCtlgProdConfigur
            ->getGiftcardOptions($this->getItem());
    }

    /**
     * Return gift card and custom options array
     *
     * @return array
     */
    public function getOptionList()
    {
        return $this->_giftCardCtlgProdConfigur
            ->getOptions($this->getItem());
    }
}

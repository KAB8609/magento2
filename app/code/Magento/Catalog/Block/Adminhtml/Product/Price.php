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
 * Product price block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 */
namespace Magento\Catalog\Block\Adminhtml\Product;

class Price extends \Magento\Catalog\Block\Product\Price
{
    /**
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        parent::__construct($storeManager, $catalogData, $taxData, $coreData, $context, $registry, $data);
    }

    /**
     * @param null|string|bool|int|\Magento\Core\Model\Store $storeId
     * @return bool|\Magento\Core\Model\Website
     */
    public function getWebsite($storeId)
    {
        return $this->_storeManager->getStore($storeId)->getWebsite();
    }
}

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
 * Adminhtml catalog product composite configure block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Composite;

class Configure extends \Magento\Adminhtml\Block\Widget
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    protected $_template = 'catalog/product/composite/configure.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_catalogProduct;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Catalog\Model\Product $product,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_catalogProduct = $product;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Retrieve product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            if ($this->_coreRegistry->registry('current_product')) {
                $this->_product = $this->_coreRegistry->registry('current_product');
            } else {
                $this->_product = $this->_catalogProduct;
            }
        }
        return $this->_product;
    }

    /**
     * Set product object
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Block\Adminhtml\Product\Composite\Configure
     */
    public function setProduct(\Magento\Catalog\Model\Product $product = null)
    {
        $this->_product = $product;
        return $this;
    }
}
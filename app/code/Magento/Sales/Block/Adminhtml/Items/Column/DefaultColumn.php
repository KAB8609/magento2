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
 * Adminhtml sales order column renderer
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Items\Column;

class DefaultColumn extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Catalog\Model\Product\OptionFactory
     */
    protected $_optionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        array $data = array()
    ) {
        $this->_optionFactory = $optionFactory;
        parent::__construct($context, $coreData, $data);
    }


    public function getItem()
    {
        if ($this->_getData('item') instanceof \Magento\Sales\Model\Order\Item) {
            return $this->_getData('item');
        } else {
            return $this->_getData('item')->getOrderItem();
        }
    }

    public function getOrderOptions()
    {
        $result = array();
        if ($options = $this->getItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
    }

    /**
     * Return custom option html
     *
     * @param array $optionInfo
     * @return string
     */
    public function getCustomizedOptionValue($optionInfo)
    {
        // render customized option view
        $_default = $optionInfo['value'];
        if (isset($optionInfo['option_type'])) {
            try {
                $group = $this->_optionFactory->create()->groupFactory($optionInfo['option_type']);
                return $group->getCustomizedView($optionInfo);
            } catch (\Exception $e) {
                return $_default;
            }
        }
        return $_default;
    }

    public function getSku()
    {
        /*if ($this->getItem()->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE) {
            return $this->getItem()->getProductOptionByCode('simple_sku');
        }*/
        return $this->getItem()->getSku();
    }

}

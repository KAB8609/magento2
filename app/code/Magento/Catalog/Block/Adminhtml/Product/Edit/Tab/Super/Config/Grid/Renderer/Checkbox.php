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
 * Adminhtml catalog super product link grid checkbox renderer
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Super\Config\Grid\Renderer;

class Checkbox extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\Checkbox
{
    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter,
        \Magento\Core\Helper\Data $coreData,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        parent::__construct($context, $converter, $data);
    }

    /**
     * Renders grid column
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        $result = parent::render($row);
        return $result.'<input type="hidden" class="value-json" value="'.htmlspecialchars($this->getAttributesJson($row)).'" />';
    }

    public function getAttributesJson(\Magento\Object $row)
    {
        if(!$this->getColumn()->getAttributes()) {
            return '[]';
        }

        $result = array();
        foreach($this->getColumn()->getAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            if($productAttribute->getSourceModel()) {
                $label = $productAttribute->getSource()->getOptionText($row->getData($productAttribute->getAttributeCode()));
            } else {
                $label = $row->getData($productAttribute->getAttributeCode());
            }
            $item = array();
            $item['label']        = $label;
            $item['attribute_id'] = $productAttribute->getId();
            $item['value_index']  = $row->getData($productAttribute->getAttributeCode());
            $result[] = $item;
        }

        return $this->_coreData->jsonEncode($result);
    }
}// Class \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Super\Config\Grid\Renderer\Checkbox END
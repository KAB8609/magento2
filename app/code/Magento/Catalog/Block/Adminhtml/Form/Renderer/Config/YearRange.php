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
 * Catalog Custom Options Config Renderer
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Form\Renderer\Config;

class YearRange extends \Magento\Backend\Block\System\Config\Form\Field
{

    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $element->setStyle('width:70px;')
            ->setName($element->getName() . '[]');

        if ($element->getValue()) {
            $values = explode(',', $element->getValue());
        } else {
            $values = array();
        }

        $from = $element->setValue(isset($values[0]) ? $values[0] : null)->getElementHtml();
        $to = $element->setValue(isset($values[1]) ? $values[1] : null)->getElementHtml();
        return __('<label class="label"><span>from</span></label>') . $from
            . __('<label class="label"><span>to</span></label>') . $to;
    }
}
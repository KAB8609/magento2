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
 * Base block for rendering category and product forms
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Catalog;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected function _prepareLayout()
    {
        \Magento\Data\Form::setElementRenderer(
            $this->getLayout()->createBlock(
                'Magento\Adminhtml\Block\Widget\Form\Renderer\Element',
                $this->getNameInLayout() . '_element'
            )
        );
        \Magento\Data\Form::setFieldsetRenderer(
            $this->getLayout()->createBlock(
                'Magento\Adminhtml\Block\Widget\Form\Renderer\Fieldset',
                $this->getNameInLayout() . '_fieldset'
            )
        );
        \Magento\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Magento\Catalog\Block\Adminhtml\Catalog\Form\Renderer\Fieldset\Element',
                $this->getNameInLayout() . '_fieldset_element'
            )
        );
    }
}

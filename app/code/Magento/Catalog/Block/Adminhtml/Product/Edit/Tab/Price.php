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
 * Adminhtml product edit price block
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab;

class Price extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected function _prepareForm()
    {
        $product = $this->_coreRegistry->registry('product');

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('tiered_price', array('legend' => __('Tier Pricing')));

        $fieldset->addField('default_price', 'label', array(
                'label'=> __('Default Price'),
                'title'=> __('Default Price'),
                'name'=>'default_price',
                'bold'=>true,
                'value'=>$product->getPrice()
        ));

        $fieldset->addField('tier_price', 'text', array(
                'name'=>'tier_price',
                'class'=>'requried-entry',
                'value'=>$product->getData('tier_price')
        ));

        $form->getElement('tier_price')->setRenderer(
            $this->getLayout()->createBlock('Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Price\Tier')
        );

        $this->setForm($form);
    }
}// Class \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Price END
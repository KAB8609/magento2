<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * description
 *
 * @category    Mage
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Actions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Actions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_promo_quote_rule');

        //$form = new Varien_Data_Form(array('id' => 'edit_form1', 'action' => $this->getData('action'), 'method' => 'post'));
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array('legend'=>__('Update prices using the following information')));

        $fieldset->addField('simple_action', 'select', array(
            'label'     => __('Apply'),
            'name'      => 'simple_action',
            'options'    => array(
                Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION => __('Percent of product price discount'),
                Mage_SalesRule_Model_Rule::BY_FIXED_ACTION => __('Fixed amount discount'),
                Mage_SalesRule_Model_Rule::CART_FIXED_ACTION => __('Fixed amount discount for whole cart'),
                Mage_SalesRule_Model_Rule::BUY_X_GET_Y_ACTION => __('Buy X get Y free (discount amount is Y)'),
            ),
        ));
        $fieldset->addField('discount_amount', 'text', array(
            'name' => 'discount_amount',
            'required' => true,
            'class' => 'validate-not-negative-number',
            'label' => __('Discount Amount'),
        ));
        $model->setDiscountAmount($model->getDiscountAmount()*1);

        $fieldset->addField('discount_qty', 'text', array(
            'name' => 'discount_qty',
            'label' => __('Maximum Qty Discount is Applied To'),
        ));
        $model->setDiscountQty($model->getDiscountQty()*1);

        $fieldset->addField('discount_step', 'text', array(
            'name' => 'discount_step',
            'label' => __('Discount Qty Step (Buy X)'),
        ));

        $fieldset->addField('apply_to_shipping', 'select', array(
            'label'     => __('Apply to Shipping Amount'),
            'title'     => __('Apply to Shipping Amount'),
            'name'      => 'apply_to_shipping',
            'values'    => Mage::getSingleton('Mage_Backend_Model_Config_Source_Yesno')->toOptionArray(),
        ));

        $fieldset->addField('simple_free_shipping', 'select', array(
            'label'     => __('Free Shipping'),
            'title'     => __('Free Shipping'),
            'name'      => 'simple_free_shipping',
            'options'    => array(
                0 => __('No'),
                Mage_SalesRule_Model_Rule::FREE_SHIPPING_ITEM => __('For matching items only'),
                Mage_SalesRule_Model_Rule::FREE_SHIPPING_ADDRESS => __('For shipment with matching items'),
            ),
        ));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => __('Stop Further Rules Processing'),
            'title'     => __('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'    => array(
                '1' => __('Yes'),
                '0' => __('No'),
            ),
        ));

        $renderer = Mage::getBlockSingleton('Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/promo_quote/newActionHtml/form/rule_actions_fieldset'));

        $fieldset = $form->addFieldset('actions_fieldset', array(
            'legend'=>__('Apply the rule only to cart items matching the following conditions (leave blank for all items).')
        ))->setRenderer($renderer);

        $fieldset->addField('actions', 'text', array(
            'name' => 'actions',
            'label' => __('Apply To'),
            'title' => __('Apply To'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('Mage_Rule_Block_Actions'));

        Mage::dispatchEvent('adminhtml_block_salesrule_actions_prepareform', array('form' => $form));

        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }
        //$form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }

}

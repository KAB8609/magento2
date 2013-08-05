<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Adminhtml Edit Tab Actions Block
 *
 * @category   Enterprise
 * @package    Enterprise_TargetRule
 */
class Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Actions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface

{
    /**
     * Prepare target rule actions form before rendering HTML
     *
     * @return Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Actions
     */
    protected function _prepareForm()
    {
        /* @var $model Enterprise_TargetRule_Model_Rule */
        $model  = Mage::registry('current_target_rule');
        $form   = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');

        $fieldset   = $form->addFieldset('actions_fieldset', array(
            'legend' => __('Product Result Conditions (leave blank for matching all products)'))
        );
        $newCondUrl = $this->getUrl('*/targetrule/newActionsHtml/', array(
            'form'  => $fieldset->getHtmlId()
        ));
        $renderer   = Mage::getBlockSingleton('Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset')
            ->setTemplate('Enterprise_TargetRule::edit/conditions/fieldset.phtml')
            ->setNewChildUrl($newCondUrl);
        $fieldset->setRenderer($renderer);

        $element    = $fieldset->addField('actions', 'text', array(
            'name'      => 'actions',
            'required'  => true
        ));
        $element->setRule($model);
        $element->setRenderer(Mage::getBlockSingleton('Enterprise_TargetRule_Block_Adminhtml_Actions_Conditions'));

        $model->getActions()->setJsFormObject($fieldset->getHtmlId());
        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Products to Display');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Products to Display');
    }

    /**
     * Check is can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Intialize form
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('enterprise_giftregistry_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Gift Registry'));
    }

    /**
     * Add tab sections
     *
     * @return Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label'   => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('General Information'),
            'content' => $this->getLayout()->createBlock(
                'Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Tab_General'
            )->toHtml()
        ));

        $this->addTab('registry_attributes', array(
            'label'   => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Attributes'),
            'content' => $this->getLayout()->createBlock(
                'Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Tab_Registry'
            )->toHtml()
        ));

        return parent::_beforeToHtml();
    }

}

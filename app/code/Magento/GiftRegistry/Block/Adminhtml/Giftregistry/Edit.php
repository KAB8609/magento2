<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Intialize form
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_GiftRegistry';
        $this->_controller = 'adminhtml_giftregistry';

        parent::_construct();

        if (Mage::registry('current_giftregistry_type')) {
            $this->_updateButton('save', 'label', __('Save'));
            $this->_updateButton('save', 'data_attribute', array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#edit_form'),
                )
            ));

            $confirmMessage = __("If you delete this gift registry type, you also delete customer registries that use this type. Do you want to continue?");
            $this->_updateButton('delete', 'label', __('Delete'));
            $this->_updateButton('delete', 'onclick',
                'deleteConfirm(\'' . $this->jsQuoteEscape($confirmMessage) . '\', \'' . $this->getDeleteUrl() . '\')'
            );

            $this->_addButton('save_and_continue_edit', array(
                'class'   => 'save',
                'label'   => __('Save and Continue Edit'),
                'data_attribute' => array(
                    'mage-init' => array(
                        'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                    ),
                ),
            ), 3);
        }
    }

    /**
     * Return form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $type = Mage::registry('current_giftregistry_type');
        if ($type->getId()) {
            return __("Edit '%1' Gift Registry Type", $this->escapeHtml($type->getLabel()));
        }
        else {
            return __('New Gift Registry Type');
        }
    }

    /**
     * Return save url
     *
     * @return string
     */
    public function getSaveUrl()
    {
        $type = Mage::registry('current_giftregistry_type');
        return $this->getUrl('*/*/save', array('id' => $type->getId(), 'store' => $type->getStoreId()));
    }
}
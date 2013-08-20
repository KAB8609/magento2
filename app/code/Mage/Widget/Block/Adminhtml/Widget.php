<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * WYSIWYG widget plugin main block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Block_Adminhtml_Widget extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'Mage_Widget';
        $this->_controller = 'adminhtml';
        $this->_mode = 'widget';
        $this->_headerText = __('Widget Insertion');

        $this->removeButton('reset');
        $this->removeButton('back');
        $this->_updateButton('save', 'label', __('Insert Widget'));
        $this->_updateButton('save', 'class', 'add-widget');
        $this->_updateButton('save', 'id', 'insert_button');
        $this->_updateButton('save', 'onclick', 'wWidget.insertWidget()');

        $this->_formScripts[] = 'wWidget = new WysiwygWidget.Widget('
            . '"widget_options_form", "select_widget_type", "widget_options", "'
            . $this->getUrl('*/*/loadOptions') .'", "' . $this->getRequest()->getParam('widget_target_id') . '");';
    }
}

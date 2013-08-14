<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * WYSIWYG widget plugin main block
 *
 * @category   Mage
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Widget_Block_Adminhtml_Widget extends Magento_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'Magento_Widget';
        $this->_controller = 'adminhtml';
        $this->_mode = 'widget';
        $this->_headerText = $this->helper('Magento_Widget_Helper_Data')->__('Widget Insertion');

        $this->removeButton('reset');
        $this->removeButton('back');
        $this->_updateButton('save', 'label', $this->helper('Magento_Widget_Helper_Data')->__('Insert Widget'));
        $this->_updateButton('save', 'class', 'add-widget');
        $this->_updateButton('save', 'id', 'insert_button');
        $this->_updateButton('save', 'onclick', 'wWidget.insertWidget()');

        $this->_formScripts[] = 'wWidget = new WysiwygWidget.Widget('
            . '"widget_options_form", "select_widget_type", "widget_options", "'
            . $this->getUrl('*/*/loadOptions') .'", "' . $this->getRequest()->getParam('widget_target_id') . '");';
    }
}

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
 * Admin CMS page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Cms_Page_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId   = 'page_id';
        $this->_controller = 'cms_page';

        parent::_construct();

        if ($this->_isAllowedAction('Mage_Cms::save')) {
            $this->_updateButton('save', 'label', Mage::helper('Mage_Cms_Helper_Data')->__('Save Page'));
            $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Save and Continue Edit'),
                'class'     => 'save',
                'data_attribute'  => array(
                    'mage-init' => array(
                        'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                    ),
                ),
            ), -100);
        } else {
            $this->_removeButton('save');
        }

        if ($this->_isAllowedAction('Mage_Cms::page_delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('Mage_Cms_Helper_Data')->__('Delete Page'));
        } else {
            $this->_removeButton('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('cms_page')->getId()) {
            return Mage::helper('Mage_Cms_Helper_Data')->__("Edit Page '%s'", $this->escapeHtml(Mage::registry('cms_page')->getTitle()));
        }
        else {
            return Mage::helper('Mage_Cms_Helper_Data')->__('New Page');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'active_tab' => '{{tab_id}}'
        ));
    }

    /**
     * Prepare layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $tabsBlock = $this->getLayout()->getBlock('cms_page_edit_tabs');
        if ($tabsBlock) {
            $tabsBlockJsObject = $tabsBlock->getJsObjectName();
            $tabsBlockPrefix   = $tabsBlock->getId() . '_';
        } else {
            $tabsBlockJsObject = 'page_tabsJsTabs';
            $tabsBlockPrefix   = 'page_tabs_';
        }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            }
            jQuery(function() {
                var tabsElement = jQuery(\"#" . $tabsBlock->getId() ."\");
                tabsElement.on('tabscreate', function() {
                    tabsElement
                        .tabs('option', 'tabsBlockPrefix', '" . $tabsBlockPrefix . "')
                        .tabs('option', 'tabIdArgument', 'active_tab');
                });
            });
        ";
        return parent::_prepareLayout();
    }
}

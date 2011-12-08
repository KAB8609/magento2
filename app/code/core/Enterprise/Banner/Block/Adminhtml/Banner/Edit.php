<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Banner_Block_Adminhtml_Banner_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize banner edit page. Set management buttons
     *
     */
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_banner';
        $this->_blockGroup = 'Enterprise_Banner';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('Enterprise_Banner_Helper_Data')->__('Save Banner'));
        $this->_updateButton('delete', 'label', Mage::helper('Enterprise_Banner_Helper_Data')->__('Delete Banner'));

        $this->_addButton('save_and_edit_button', array(
                'label'   => Mage::helper('Enterprise_Banner_Helper_Data')->__('Save and Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save'
            ), 100
        );
        $this->_formScripts[] = 'function saveAndContinueEdit() {
            editForm.submit($(\'edit_form\').action + \'back/edit/\');}';
    }

    /**
     * Get current loaded banner ID
     *
     */
    public function getBannerId()
    {
        return Mage::registry('current_banner')->getId();
    }

    /**
     * Get header text for banenr edit page
     *
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_banner')->getId()) {
            return $this->escapeHtml(Mage::registry('current_banner')->getName());
        } else {
            return Mage::helper('Enterprise_Banner_Helper_Data')->__('New Banner');
        }
    }

    /**
     * Get form action URL
     *
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }
}

<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Edit revision page
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Page_Revision_Edit extends Mage_Adminhtml_Block_Cms_Page_Edit
{

    /**
     * Constructor. Modifying default CE buttons.
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page_Revision_Edit
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'revision_id';

        $this->_controller = 'adminhtml_cms_page_revision';
        $this->_blockGroup = 'enterprise_cms';

        $this->setFormActionUrl($this->getUrl('*/cms_page_revision/save'));

        $this->_addButton('preview', array(
            'label'     => Mage::helper('enterprise_cms')->__('Preview'),
            'onclick'   => "previewAction('edit_form', editForm, '" . $this->getPreviewUrl() . "')",
            'class'     => 'preview',
        ));


        if ($this->_isAllowedAction('publish_revision')) {
            $this->_addButton('publish', array(
                'id'        => 'publish_button',
                'label'     => Mage::helper('enterprise_cms')->__('Publish'),
                'onclick'   => "publishAction('" . $this->getPublishUrl() . "')",
                'class'     => 'publish',
            ));

            $this->_addButton('save_publish', array(
                'id'        => 'save_publish_button',
                'label'     => Mage::helper('enterprise_cms')->__('Save And Publish'),
                'onclick'   => "saveAndPublishAction(editForm, '" . $this->getSaveUrl() . "')",
                'class'     => 'publish no-display',
            ));
        }

        if ($this->_isAllowedAction('save_revision')) {
            $this->_updateButton('save', 'label', Mage::helper('enterprise_cms')->__('Save'));
            $this->_updateButton('save', 'onclick', 'editForm.submit(\'' . $this->getSaveUrl() . '\');');
            $this->_updateButton('saveandcontinue', 'onclick', 'editForm.submit(\'' . $this->getSaveUrl() . '\'+\'back/edit/\');');

            // Adding button to create new version
            $this->_addButton('new_version', array(
                'id'        => 'new_version',
                'label'     => Mage::helper('enterprise_cms')->__('New Version'),
                'onclick'   => 'newVersionAction()',
                'class'     => 'new',
            ));

            $this->_formScripts[] = "
                function newVersionAction(){
                    var versionLabel = prompt('" . Mage::helper('enterprise_cms')->__('Specify label for new version (required)') . "', '')
                    if (versionLabel == '') {
                        alert('" . Mage::helper('enterprise_cms')->__('You should specify valid label') . "');
                        return false;
                    } else if (versionLabel == null) {
                        return false;
                    }

                    $('page_label').value = versionLabel;
                    $('page_create_new_version_action').value = '1';
                    editForm.submit('" . $this->getSaveUrl() . "');
                }
            ";

        } else {
            $this->removeButton('save');
            $this->removeButton('saveandcontinue');
        }

        if ($this->_isAllowedAction('delete_revision')) {
            $this->_updateButton('delete', 'label', Mage::helper('enterprise_cms')->__('Delete'));
        } else {
            $this->removeButton('delete');
        }

        return $this;
    }

    /**
     * Retrieve text for header element depending
     * on loaded page and revision
     *
     * @return string
     */
    public function getHeaderText()
    {
        $revisionNumber = Mage::registry('cms_page')->getRevisionNumber();
        $title = $this->htmlEscape(Mage::registry('cms_page')->getTitle());

        return Mage::helper('enterprise_cms')->__("Edit Page '%s' Revision #%s", $title, $this->htmlEscape($revisionNumber));
    }

    /**
     * Check permission for passed action
     * Rewrite CE save permission to EE save_revision
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        if ($action == 'save') {
            $action = 'save_revision';
        }
        return parent::_isAllowedAction($action);
    }

    /**
     * Get URL for back button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/cms_page_version/edit',
             array(
                'page_id' => Mage::registry('cms_page')->getPageId(),
                'version_id' => Mage::registry('cms_page')->getVersionId()
             ));
    }

    /**
     * Get URL for delete button
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current' => true));
    }

    /**
     * Get URL for publish button
     *
     * @return string
     */
    public function getPublishUrl()
    {
        return $this->getUrl('*/*/publish', array('_current' => true));
    }

    /**
     * Get URL for preview button
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->getUrl('*/*/preview');
    }

    /**
     * Adding info block html before form html
     *
     * @return string
     */
    public function getFormHtml()
    {
        return $this->getChildHtml('revision_info') . parent::getFormHtml();
    }
}

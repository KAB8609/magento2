<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
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
    protected function _construct()
    {
        parent::_construct();

        $this->removeButton('delete');

        $this->_objectId = 'revision_id';

        $this->_controller = 'adminhtml_cms_page_revision';
        $this->_blockGroup = 'Enterprise_Cms';

        /* @var $config Enterprise_Cms_Model_Config */
        $config = Mage::getSingleton('Enterprise_Cms_Model_Config');

        $this->setFormActionUrl($this->getUrl('*/cms_page_revision/save'));

        $objId = $this->getRequest()->getParam($this->_objectId);

        if (!empty($objId) && $config->canCurrentUserDeleteRevision()) {
            $this->_addButton('delete_revision', array(
                'label'     => Mage::helper('Enterprise_Cms_Helper_Data')->__('Delete'),
                'class'     => 'delete',
                'onclick'   => 'deleteConfirm(\''. Mage::helper('Enterprise_Cms_Helper_Data')->__('Are you sure you want to delete this revision?')
                                .'\', \'' . $this->getDeleteUrl() . '\')',
            ));
        }

        $this->_addButton('preview', array(
            'label'     => Mage::helper('Enterprise_Cms_Helper_Data')->__('Preview'),
            'class'     => 'preview',
            'data_attr'  => array(
                'widget-button' => array(
                    'event' => 'preview',
                    'related' => '#edit_form',
                    'eventData' => array(
                        'action' => $this->getPreviewUrl()
                    )
                )
            )
        ));

        if ($config->canCurrentUserPublishRevision()) {
            $this->_addButton('publish', array(
                'id'        => 'publish_button',
                'label'     => Mage::helper('Enterprise_Cms_Helper_Data')->__('Publish'),
                'onclick'   => "publishAction('" . $this->getPublishUrl() . "')",
                'class'     => 'publish' . (Mage::registry('cms_page')->getId()? '' : ' no-display'),
            ), 1);

            if ($config->canCurrentUserSaveRevision()) {
                $this->_addButton('save_publish', array(
                    'id'        => 'save_publish_button',
                    'label'     => Mage::helper('Enterprise_Cms_Helper_Data')->__('Save and Publish'),
                    'onclick'   => "saveAndPublishAction(editForm, '" . $this->getSaveUrl() . "')",
                    'class'     => 'publish no-display',
                    'data_attr'  => array(
                        'widget-button' => array('event' => 'saveAndPublish', 'related' => '#edit_form')
                    )
                ), 1);
            }

            $this->_updateButton('saveandcontinue', 'level', 2);
        }

        if ($config->canCurrentUserSaveRevision()) {
            $this->_updateButton('save', 'label', Mage::helper('Enterprise_Cms_Helper_Data')->__('Save'));
            $this->_updateButton('save', 'data_attr', array(
                'widget-button' => array('event' => 'save', 'related' => '#edit_form')
            ));
            $this->_updateButton(
                'saveandcontinue',
                'data_attr',
                array(
                    'widget-button' => array('event' => 'preview', 'related' => '#edit_form')
                )
            );

            $page = Mage::registry('cms_page');
            // Adding button to create new version
            $this->_addButton('new_version', array(
                'id'        => 'new_version',
                'label'     => Mage::helper('Enterprise_Cms_Helper_Data')->__('Save in New Version...'),
                'data_attr'  => array(
                    'widget-button' => array(
                        'event' => 'save',
                        'related' => '#edit_form',
                        'eventData' => array(
                            'action' => $this->getNewVersionUrl(),
                            'target' => 'cms-page-preview-' . ($page ? $page->getPageId() : ''),
                        )
                    )
                ),
                'class'     => 'new',
            ));

            $this->_formScripts[] = "
                function newVersionAction(e){
                    var versionName = prompt('" . Mage::helper('Enterprise_Cms_Helper_Data')->__('Specify New Version Name (required)') . "', '')
                    if (versionName == '') {
                        alert('" . Mage::helper('Enterprise_Cms_Helper_Data')->__('You should specify valid name') . "');
                        e.stopImmediatePropagation();
                    } else if (versionName == null) {
                        return false;
                        e.stopImmediatePropagation();
                    }
                    $('page_label').value = versionName;
                }
                (function($){
                    $('#new_version').on('click', newVersionAction);
                })(jQuery)
            ";

        } else {
            $this->removeButton('save');
            $this->removeButton('saveandcontinue');
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
        $title = $this->escapeHtml(Mage::registry('cms_page')->getTitle());

        if ($revisionNumber) {
            return Mage::helper('Enterprise_Cms_Helper_Data')->__("Edit Page '%s' Revision #%s", $title, $this->escapeHtml($revisionNumber));
        } else {
            return Mage::helper('Enterprise_Cms_Helper_Data')->__("Edit Page '%s' New Revision", $title);
        }
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
        if ($action == 'Mage_Cms::save') {
            $action = 'Enterprise_Cms::save_revision';
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
        $page = Mage::registry('cms_page');
        return $this->getUrl('*/cms_page_version/edit',
             array(
                'page_id' => $page ? $page->getPageId() : null,
                'version_id' => $page ? $page->getVersionId() : null
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

    /**
     * Save into new version link
     *
     * @return string
     */
    public function getNewVersionUrl()
    {
        return $this->getUrl('*/cms_page_version/new');
    }
}

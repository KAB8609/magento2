<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Edit version page
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_VersionsCms_Block_Adminhtml_Cms_Page_Version_Edit
    extends Magento_Adminhtml_Block_Widget_Form_Container
{
    protected $_objectId   = 'version_id';
    protected $_blockGroup = 'Magento_VersionsCms';
    protected $_controller = 'adminhtml_cms_page_version';

    /**
     * Constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $version = Mage::registry('cms_page_version');

        $config = Mage::getSingleton('Magento_VersionsCms_Model_Config');
        /* @var $config Magento_VersionsCms_Model_Config */

        // Add 'new button' depending on permission
        if ($config->canCurrentUserSaveVersion()) {
            $this->_addButton('new', array(
                    'label'     => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Save as new version.'),
                    'class'     => 'new',
                    'data_attribute'  => array(
                        'mage-init' => array(
                            'button' => array(
                                'event' => 'save',
                                'target' => '#edit_form',
                                'eventData' => array(
                                    'action' => $this->getNewUrl()
                                )
                            ),
                        ),
                    ),
                ));

            $this->_addButton('new_revision', array(
                    'label'     => Mage::helper('Magento_VersionsCms_Helper_Data')->__('New Revision...'),
                    'onclick'   => "setLocation('" . $this->getNewRevisionUrl() . "');",
                    'class'     => 'new',
                ));
        }

        $isOwner = $version ? $config->isCurrentUserOwner($version->getUserId()) : false;
        $isPublisher = $config->canCurrentUserPublishRevision();

        // Only owner can remove version if he has such permissions
        if (!$isOwner || !$config->canCurrentUserDeleteVersion()) {
            $this->removeButton('delete');
        }

        // Only owner and publisher can save version
        if (($isOwner || $isPublisher) && $config->canCurrentUserSaveVersion()) {
            $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Save and continue edit.'),
                'class'     => 'save',
                'data_attribute'  => array(
                    'mage-init' => array(
                        'button' => array(
                            'event' => 'saveAndContinueEdit', 'target' => '#edit_form'
                        ),
                    ),
                ),
            ), 1);
        } else {
            $this->removeButton('save');
        }
    }

    /**
     * Retrieve text for header element depending
     * on loaded page and version
     *
     * @return string
     */
    public function getHeaderText()
    {
        $versionLabel = $this->escapeHtml(Mage::registry('cms_page_version')->getLabel());
        $title = $this->escapeHtml(Mage::registry('cms_page')->getTitle());

        if (!$versionLabel) {
            $versionLabel = Mage::helper('Magento_VersionsCms_Helper_Data')->__('N/A');
        }

        return Mage::helper('Magento_VersionsCms_Helper_Data')->__("Edit Page '%s' Version '%s'", $title, $versionLabel);
    }

    /**
     * Get URL for back button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/cms_page/edit',
             array(
                'page_id' => Mage::registry('cms_page') ? Mage::registry('cms_page')->getPageId() : null,
                'tab' => 'versions'
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
     * Get URL for new button
     *
     * @return string
     */
    public function getNewUrl()
    {
        return $this->getUrl('*/*/new', array('_current' => true));
    }

    /**
     * Get Url for new revision button
     *
     * @return string
     */
    public function getNewRevisionUrl()
    {
        return $this->getUrl('*/cms_page_revision/new', array('_current' => true));
    }
}

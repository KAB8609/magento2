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
 * Cms page edit form revisions tab
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Block_Adminhtml_Cms_Page_Revision_Edit_Info extends Magento_Adminhtml_Block_Widget_Container
{
    /**
     * Currently loaded page model
     *
     * @var Eanterprise_Cms_Model_Page
     */
    protected $_page;

    protected function _construct()
    {
        parent::_construct();
        $this->_page = Mage::registry('cms_page');
    }

    /**
     * Prepare version identifier. It should be
     * label or id if first one not assigned.
     * Also can be N/A.
     *
     * @return string
     */
    public function getVersion()
    {
        $version = '';
        if ($this->_page->getLabel()) {
            $version = $this->_page->getLabel();
        } else {
            $version = $this->_page->getVersionId();
        }
        return $version ? $version : Mage::helper('Enterprise_Cms_Helper_Data')->__('N/A');
    }

    /**
     * Prepare version number.
     *
     * @return string
     */
    public function getVersionNumber()
    {
        return $this->_page->getVersionNumber() ? $this->_page->getVersionNumber()
            : Mage::helper('Enterprise_Cms_Helper_Data')->__('N/A');
    }

    /**
     * Prepare version label.
     *
     * @return string
     */
    public function getVersionLabel()
    {
        return $this->_page->getLabel() ? $this->_page->getLabel()
            : Mage::helper('Enterprise_Cms_Helper_Data')->__('N/A');
    }

    /**
     * Prepare revision identifier.
     *
     * @return string
     */
    public function getRevisionId()
    {
        return $this->_page->getRevisionId() ? $this->_page->getRevisionId()
            : Mage::helper('Enterprise_Cms_Helper_Data')->__('N/A');
    }

    /**
     * Prepare revision number.
     *
     * @return string
     */
    public function getRevisionNumber()
    {
        return $this->_page->getRevisionNumber();
    }

    /**
     * Prepare author identifier.
     *
     * @return string
     */
    public function getAuthor()
    {
        $userId = $this->_page->getUserId();
        if (Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getUser()->getId() == $userId) {
            return Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getUser()->getUsername();
        }

        $user = Mage::getModel('Mage_User_Model_User')
            ->load($userId);

        if ($user->getId()) {
            return $user->getUsername();
        }
        return Mage::helper('Enterprise_Cms_Helper_Data')->__('N/A');
    }

    /**
     * Prepare time of creation for current revision.
     *
     * @return string
     */
    public function getCreatedAt()
    {
        $format = Mage::app()->getLocale()->getDateTimeFormat(
                Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM
            );
        $data = $this->_page->getRevisionCreatedAt();
        try {
            $data = Mage::app()->getLocale()->date($data, Magento_Date::DATETIME_INTERNAL_FORMAT)->toString($format);
        } catch (Exception $e) {
            $data = Mage::helper('Enterprise_Cms_Helper_Data')->__('N/A');
        }
        return  $data;
    }
}

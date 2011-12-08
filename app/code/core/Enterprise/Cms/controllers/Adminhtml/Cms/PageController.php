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
 * Cms manage pages controller
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

require_once  'Mage/Adminhtml/controllers/Cms/PageController.php';

class Enterprise_Cms_Adminhtml_Cms_PageController extends Mage_Adminhtml_Cms_PageController
{
    protected $_handles = array();

    /**
     * Init actions
     *
     * @return Enterprise_Cms_Adminhtml_Cms_PageController
     */
    protected function _initAction()
    {
        $update = $this->getLayout()->getUpdate();
        $update->addHandle('default');

        // add default layout handles for this action
        $this->addActionLayoutHandles();
        $update->addHandle($this->_handles);

        $this->loadLayoutUpdates()
            ->generateLayoutXml()
            ->generateLayoutBlocks();

        $this->_initLayoutMessages('Mage_Adminhtml_Model_Session');

        //load layout, set active menu and breadcrumbs
        $this->_setActiveMenu('cms/page')
            ->_addBreadcrumb(Mage::helper('Mage_Cms_Helper_Data')->__('CMS'), Mage::helper('Mage_Cms_Helper_Data')->__('CMS'))
            ->_addBreadcrumb(Mage::helper('Mage_Cms_Helper_Data')->__('Manage Pages'), Mage::helper('Mage_Cms_Helper_Data')->__('Manage Pages'));

        $this->_isLayoutLoaded = true;

        return $this;
    }

    /**
     * Prepare and place cms page model into registry
     * with loaded data if id parameter present
     *
     * @param string $idFieldName
     * @return Enterprise_Cms_Model_Page
     */
    protected function _initPage()
    {
        $this->_title($this->__('CMS'))->_title($this->__('Pages'));

        $pageId = (int) $this->getRequest()->getParam('page_id');
        $page = Mage::getModel('Mage_Cms_Model_Page');

        if ($pageId) {
            $page->load($pageId);
        }

        Mage::register('cms_page', $page);
        return $page;
    }


    /**
     * Edit CMS page
     */
    public function editAction()
    {
        $page = $this->_initPage();

        $data = Mage::getSingleton('Mage_Adminhtml_Model_Session')->getFormData(true);
        if (! empty($data)) {
            $page->setData($data);
        }

        if ($page->getId()){
            if ($page->getUnderVersionControl()) {
                $this->_handles[] = 'adminhtml_cms_page_edit_changes';
            }
        } else if (!$page->hasUnderVersionControl()) {
            $page->setUnderVersionControl((int)Mage::getSingleton('Enterprise_Cms_Model_Config')->getDefaultVersioningStatus());
        }

        $this->_title($page->getId() ? $page->getTitle() : $this->__('New Page'));

        $this->_initAction()
            ->_addBreadcrumb($page->getId() ? Mage::helper('Mage_Cms_Helper_Data')->__('Edit Page')
                    : Mage::helper('Mage_Cms_Helper_Data')->__('New Page'),
                $page->getId() ? Mage::helper('Mage_Cms_Helper_Data')->__('Edit Page')
                    : Mage::helper('Mage_Cms_Helper_Data')->__('New Page'));

        $this->renderLayout();
    }

    /**
     * Action for versions ajax tab
     *
     * @return Enterprise_Cms_Adminhtml_Cms_Page_RevisionController
     */
    public function versionsAction()
    {
        $this->_initPage();

        $this->loadLayout();
        $this->renderLayout();

        return $this;
    }

    /**
     * Mass deletion for versions
     *
     */
    public function massDeleteVersionsAction()
    {
        $ids = $this->getRequest()->getParam('version');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select version(s).'));
        }
        else {
            try {
                $userId = Mage::getSingleton('Mage_Admin_Model_Session')->getUser()->getId();
                $accessLevel = Mage::getSingleton('Enterprise_Cms_Model_Config')->getAllowedAccessLevel();

                foreach ($ids as $id) {
                    $version = Mage::getSingleton('Enterprise_Cms_Model_Page_Version')
                        ->loadWithRestrictions($accessLevel, $userId, $id);

                    if ($version->getId()) {
                        $version->delete();
                    }
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been deleted', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError(Mage::helper('Enterprise_Cms_Helper_Data')->__('An error occurred while deleting versions.'));
            }
        }
        $this->_redirect('*/*/edit', array('_current' => true, 'tab' => 'versions'));

        return $this;
    }

    /**
     * Check the permission to run action.
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'massDeleteVersions':
                return Mage::getSingleton('Enterprise_Cms_Model_Config')->canCurrentUserDeleteVersion();
                break;
            default:
                return parent::_isAllowed();
                break;
        }
    }
}

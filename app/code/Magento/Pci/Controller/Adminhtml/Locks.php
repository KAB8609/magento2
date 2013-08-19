<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Locked administrators controller
 *
 */
class Magento_Pci_Controller_Adminhtml_Locks extends Magento_Adminhtml_Controller_Action
{
    /**
     * Render page with grid
     *
     */
    public function indexAction()
    {
        $this->_title($this->__('Locked Users'));

        $this->loadLayout();
        $this->_setActiveMenu('Magento_Pci::system_acl_locks');
        $this->renderLayout();
    }

    /**
     * Render AJAX-grid only
     *
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();

    }

    /**
     * Unlock specified users
     */
    public function massUnlockAction()
    {
        try {
            // unlock users
            $userIds = $this->getRequest()->getPost('unlock');
            if ($userIds && is_array($userIds)) {
                $affectedUsers = Mage::getResourceSingleton('Magento_Pci_Model_Resource_Admin_User')
                    ->unlock($userIds);
                Mage::getSingleton('Magento_Adminhtml_Model_Session')
                        ->addSuccess($this->__('Unlocked %d user(s).', $affectedUsers));
            }
        }
        catch (Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    /**
     * Check whether access is allowed for current admin session
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Pci::locks');
    }
}

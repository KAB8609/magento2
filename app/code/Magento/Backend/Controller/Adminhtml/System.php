<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System admin controller
 *
 * @category   Mage
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Controller_Adminhtml_System extends Magento_Backend_Controller_ActionAbstract
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('Magento_Adminhtml::system');
        $this->_addBreadcrumb(
            $this->_helper->__('System'),
            $this->_helper->__('System')
        );
        $this->renderLayout();
    }

    public function setStoreAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        if ($storeId) {
            $this->_session->setStoreId($storeId);
        }
        $this->_redirectReferer();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::system');
    }
}

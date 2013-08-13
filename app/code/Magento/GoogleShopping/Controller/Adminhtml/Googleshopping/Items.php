<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GoogleShopping Admin Items Controller
 *
 * @category   Mage
 * @package    Magento_GoogleShopping
 * @name       Magento_GoogleShopping_Controller_Adminhtml_Googleshopping_Items
 * @author     Magento Core Team <core@magentocommerce.com>
*/
class Magento_GoogleShopping_Controller_Adminhtml_Googleshopping_Items extends Magento_Adminhtml_Controller_Action
{
    /**
     * Initialize general settings for action
     *
     * @return  Magento_GoogleShopping_Controller_Adminhtml_Googleshopping_Items
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_GoogleShopping::catalog_googleshopping_items')
            ->_addBreadcrumb(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Catalog'), Mage::helper('Magento_Adminhtml_Helper_Data')->__('Catalog'))
            ->_addBreadcrumb(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Google Content'), Mage::helper('Magento_Adminhtml_Helper_Data')->__('Google Content'));
        return $this;
    }

    /**
     * Manage Items page with two item grids: Magento products and Google Content items
     */
    public function indexAction()
    {
        $this->_title($this->__('Google Content Items'));

        if (0 === (int)$this->getRequest()->getParam('store')) {
            $this->_redirect('*/*/', array('store' => Mage::app()->getAnyStoreView()->getId(), '_current' => true));
            return;
        }

        $this->_initAction();

        $contentBlock = $this->getLayout()
            ->createBlock('Magento_GoogleShopping_Block_Adminhtml_Items')->setStore($this->_getStore());

        if ($this->getRequest()->getParam('captcha_token') && $this->getRequest()->getParam('captcha_url')) {
            $contentBlock->setGcontentCaptchaToken(
                Mage::helper('Magento_Core_Helper_Data')->urlDecode($this->getRequest()->getParam('captcha_token'))
            );
            $contentBlock->setGcontentCaptchaUrl(
                Mage::helper('Magento_Core_Helper_Data')->urlDecode($this->getRequest()->getParam('captcha_url'))
            );
        }

        if (!$this->_getConfig()->isValidDefaultCurrencyCode($this->_getStore()->getId())) {
            $_countryInfo = $this->_getConfig()->getTargetCountryInfo($this->_getStore()->getId());
            $this->_getSession()->addNotice(
                Mage::helper('Magento_GoogleShopping_Helper_Data')->__("The store's currency should be set to %s for %s in system configuration. Otherwise item prices won't be correct in Google Content.", $_countryInfo['currency_name'], $_countryInfo['name'])
            );
        }

        $this->_addBreadcrumb(Mage::helper('Magento_GoogleShopping_Helper_Data')->__('Items'), Mage::helper('Magento_GoogleShopping_Helper_Data')->__('Items'))
            ->_addContent($contentBlock)
            ->renderLayout();
    }

    /**
     * Grid with Google Content items
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('Magento_GoogleShopping_Block_Adminhtml_Items_Item')
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml()
           );
    }

    /**
     * Retrieve synchronization process mutex
     *
     * @return Magento_GoogleShopping_Model_Flag
     */
    protected function _getFlag()
    {
        return Mage::getSingleton('Magento_GoogleShopping_Model_Flag')->loadSelf();
    }

    /**
     * Add (export) several products to Google Content
     */
    public function massAddAction()
    {
        $flag = $this->_getFlag();
        if ($flag->isLocked()) {
            return;
        }

        session_write_close();
        ignore_user_abort(true);
        set_time_limit(0);

        $storeId = $this->_getStore()->getId();
        $productIds = $this->getRequest()->getParam('product', null);
        $notifier = Mage::getModel('Magento_AdminNotification_Model_Inbox');

        try {
            $flag->lock();
            Mage::getModel('Magento_GoogleShopping_Model_MassOperations')
                ->setFlag($flag)
                ->addProducts($productIds, $storeId);
        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            // Google requires CAPTCHA for login
            $this->_getSession()->addError(Mage::helper('Magento_GoogleShopping_Helper_Data')->__($e->getMessage()));
            $flag->unlock();
            $this->_redirectToCaptcha($e);
            return;
        } catch (Exception $e) {
            $flag->unlock();
            $notifier->addMajor(
                Mage::helper('Magento_GoogleShopping_Helper_Data')->__('An error has occurred while adding products to google shopping account.'),
                $e->getMessage()
            );
            Mage::logException($e);
            return;
        }

        $flag->unlock();
    }

    /**
     * Delete products from Google Content
     */
    public function massDeleteAction()
    {
        $flag = $this->_getFlag();
        if ($flag->isLocked()) {
            return;
        }

        session_write_close();
        ignore_user_abort(true);
        set_time_limit(0);

        $itemIds = $this->getRequest()->getParam('item');

        try {
            $flag->lock();
            Mage::getModel('Magento_GoogleShopping_Model_MassOperations')
                ->setFlag($flag)
                ->deleteItems($itemIds);
        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            // Google requires CAPTCHA for login
            $this->_getSession()->addError(Mage::helper('Magento_GoogleShopping_Helper_Data')->__($e->getMessage()));
            $flag->unlock();
            $this->_redirectToCaptcha($e);
            return;
        } catch (Exception $e) {
            $flag->unlock();
            Mage::getModel('Magento_AdminNotification_Model_Inbox')->addMajor(
                Mage::helper('Magento_GoogleShopping_Helper_Data')->__('An error has occurred while deleting products from google shopping account.'),
                Mage::helper('Magento_GoogleShopping_Helper_Data')->__('One or more products were not deleted from google shopping account. Refer to the log file for details.')
            );
            Mage::logException($e);
            return;
        }

        $flag->unlock();
    }

    /**
     * Update items statistics and remove the items which are not available in Google Content
     */
    public function refreshAction()
    {
        $flag = $this->_getFlag();
        if ($flag->isLocked()) {
            return;
        }

        session_write_close();
        ignore_user_abort(true);
        set_time_limit(0);

        $itemIds = $this->getRequest()->getParam('item');

        try {
            $flag->lock();
            Mage::getModel('Magento_GoogleShopping_Model_MassOperations')
                ->setFlag($flag)
                ->synchronizeItems($itemIds);
        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            // Google requires CAPTCHA for login
            $this->_getSession()->addError(Mage::helper('Magento_GoogleShopping_Helper_Data')->__($e->getMessage()));
            $flag->unlock();
            $this->_redirectToCaptcha($e);
            return;
        } catch (Exception $e) {
            $flag->unlock();
            Mage::getModel('Magento_AdminNotification_Model_Inbox')->addMajor(
                Mage::helper('Magento_GoogleShopping_Helper_Data')->__('An error has occurred while deleting products from google shopping account.'),
                Mage::helper('Magento_GoogleShopping_Helper_Data')->__('One or more products were not deleted from google shopping account. Refer to the log file for details.')
            );
            Mage::logException($e);
            return;
        }

        $flag->unlock();
    }

    /**
     * Confirm CAPTCHA
     */
    public function confirmCaptchaAction()
    {

        $storeId = $this->_getStore()->getId();
        try {
            Mage::getModel('Magento_GoogleShopping_Model_Service')->getClient(
                $storeId,
                Mage::helper('Magento_Core_Helper_Data')->urlDecode($this->getRequest()->getParam('captcha_token')),
                $this->getRequest()->getParam('user_confirm')
            );
            $this->_getSession()->addSuccess($this->__('Captcha has been confirmed.'));

        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            $this->_getSession()->addError($this->__('There was a Captcha confirmation error: %s', $e->getMessage()));
            $this->_redirectToCaptcha($e);
            return;
        } catch (Zend_Gdata_App_Exception $e) {
            $this->_getSession()->addError(
                Mage::helper('Magento_GoogleShopping_Helper_Data')->parseGdataExceptionMessage($e->getMessage())
            );
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Something went wrong during Captcha confirmation.'));
        }

        $this->_redirect('*/*/index', array('store'=>$storeId));
    }

    /**
     * Retrieve background process status
     *
     * @return Zend_Controller_Response_Abstract
     */
    public function statusAction()
    {
        if ($this->getRequest()->isAjax()) {
            $this->getResponse()->setHeader('Content-Type', 'application/json');
            $params = array(
                'is_running' => $this->_getFlag()->isLocked()
            );
            return $this->getResponse()->setBody(Mage::helper('Magento_Core_Helper_Data')->jsonEncode($params));
        }
    }

    /**
     * Redirect user to Google Captcha challenge
     *
     * @param Zend_Gdata_App_CaptchaRequiredException $e
     */
    protected function _redirectToCaptcha($e)
    {
        $redirectUrl = $this->getUrl(
            '*/*/index',
            array(
                'store' => $this->_getStore()->getId(),
                'captcha_token' => Mage::helper('Magento_Core_Helper_Data')->urlEncode($e->getCaptchaToken()),
                'captcha_url' => Mage::helper('Magento_Core_Helper_Data')->urlEncode($e->getCaptchaUrl())
            )
        );
        if ($this->getRequest()->isAjax()) {
            $this->getResponse()->setHeader('Content-Type', 'application/json')
                ->setBody(Mage::helper('Magento_Core_Helper_Data')->jsonEncode(array('redirect' => $redirectUrl)));
        } else {
            $this->_redirect($redirectUrl);
        }
    }

    /**
     * Get store object, basing on request
     *
     * @return Magento_Core_Model_Store
     * @throws Magento_Core_Exception
     */
    public function _getStore()
    {
        $store = Mage::app()->getStore((int)$this->getRequest()->getParam('store', 0));
        if ((!$store) || 0 == $store->getId()) {
            Mage::throwException($this->__('Unable to select a Store View'));
        }
        return $store;
    }

    /**
     * Get Google Shopping config model
     *
     * @return Magento_GoogleShopping_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('Magento_GoogleShopping_Model_Config');
    }

    /**
     * Check access to this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_GoogleShopping::items');
    }
}

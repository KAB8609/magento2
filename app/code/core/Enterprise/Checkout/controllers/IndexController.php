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
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Enterprise checkout index controller
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_IndexController extends Mage_Core_Controller_Front_Action
{

    /**
     * Check functionality is enabled and applicable to the Customer
     *
     * @return Enterprise_Checkout_IndexController
     */
    public function preDispatch()
    {
        if (!Mage::helper('enterprise_checkout')->isSkuEnabled()
            && !Mage::helper('enterprise_checkout')->isSkuApplied()) {
            $this->_redirect('customer/account');
        }
        parent::preDispatch();
        return $this;
    }

    /**
     * View Order by SKU page in 'My Account' section
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::helper('enterprise_checkout')->__('Order by SKU'));
        }
        $this->renderLayout();
    }

    /**
     * Upload file Action
     *
     * @return void
     */
    public function uploadFileAction()
    {
        $data = $this->getRequest()->getPost();
        $rows = array();
        $uploadError = false;
        if ($data) {
            /** @var $importModel Enterprise_Checkout_Model_Import */
            $importModel = Mage::getModel('enterprise_checkout/import');

            try {
                if ($importModel->uploadFile()) {
                    $rows = $importModel->getRows();
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addException($e, $e->getMessage());
                $uploadError = true;
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('enterprise_checkout')->__('File upload error.')
                );
                $uploadError = true;
            }

            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    if (!empty($item['sku']) && !empty($item['qty'])) {
                        $rows[] = $item;
                    }
                }
            }
            if (empty($rows) && !$uploadError) {
                $this->_getSession()->addError(Mage::helper('enterprise_checkout')->__('File is empty.'));
            } else {
                $this->getRequest()->setParam('items', $rows);
                $this->_forward('advancedAdd', 'cart');
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
}

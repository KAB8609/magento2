<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page cache admin controller
 *
 * @category    Mage
 * @package     Magento_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_PageCache_Controller_Adminhtml_PageCache extends Magento_Adminhtml_Controller_Action
{
    /**
     * Clean external cache action
     *
     * @return void
     */
    public function cleanAction()
    {
        try {
            if (Mage::helper('Magento_PageCache_Helper_Data')->isEnabled()) {
                Mage::helper('Magento_PageCache_Helper_Data')->getCacheControlInstance()->clean();
                $this->_getSession()->addSuccess(
                    Mage::helper('Magento_PageCache_Helper_Data')->__('The external full page cache has been cleaned.')
                );
            }
        }
        catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('Magento_PageCache_Helper_Data')
                    ->__('Something went wrong while clearing the external full page cache.')
            );
        }
        $this->_redirect('*/cache/index');
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_PageCache::page_cache');
    }
}

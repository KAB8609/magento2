<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CurrencySymbol
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Currency Symbols Controller
 *
 * @category    Magento
 * @package     currencysymbol
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CurrencySymbol_Controller_Adminhtml_System_Currencysymbol extends Magento_Adminhtml_Controller_Action
{
    /**
     * Show Currency Symbols Management dialog
     */
    public function indexAction()
    {
        // set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('Magento_CurrencySymbol::system_currency_symbols')
            ->_addBreadcrumb(
                Mage::helper('Magento_CurrencySymbol_Helper_Data')->__('System'),
                Mage::helper('Magento_CurrencySymbol_Helper_Data')->__('System')
            )
            ->_addBreadcrumb(
                Mage::helper('Magento_CurrencySymbol_Helper_Data')->__('Manage Currency Rates'),
                Mage::helper('Magento_CurrencySymbol_Helper_Data')->__('Manage Currency Rates')
            );

        $this->_title($this->__('Currency Symbols'));
        $this->renderLayout();
    }

    /**
     * Save custom Currency symbol
     */
    public function saveAction()
    {
        $symbolsDataArray = $this->getRequest()->getParam('custom_currency_symbol', null);
        if (is_array($symbolsDataArray)) {
            foreach ($symbolsDataArray as &$symbolsData) {
                $symbolsData = Mage::helper('Magento_Adminhtml_Helper_Data')->stripTags($symbolsData);
            }
        }

        try {
            Mage::getModel('Magento_CurrencySymbol_Model_System_Currencysymbol')->setCurrencySymbolsData($symbolsDataArray);
            Mage::getSingleton('Magento_Connect_Model_Session')->addSuccess(
                Mage::helper('Magento_CurrencySymbol_Helper_Data')->__('The custom currency symbols were applied.')
            );
        } catch (Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
        }

        $this->_redirectReferer();
    }

    /**
     * Resets custom Currency symbol for all store views, websites and default value
     */
    public function resetAction()
    {
        Mage::getModel('Magento_CurrencySymbol_Model_System_Currencysymbol')->resetValues();
        $this->_redirectReferer();
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_CurrencySymbol::symbols');
    }
}

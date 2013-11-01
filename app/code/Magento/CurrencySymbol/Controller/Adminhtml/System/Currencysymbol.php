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
namespace Magento\CurrencySymbol\Controller\Adminhtml\System;

class Currencysymbol extends \Magento\Backend\Controller\Adminhtml\Action
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
                __('System'),
                __('System')
            )
            ->_addBreadcrumb(
                __('Manage Currency Rates'),
                __('Manage Currency Rates')
            );

        $this->_title(__('Currency Symbols'));
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
                $symbolsData = $this->_objectManager->get('Magento\Adminhtml\Helper\Data')->stripTags($symbolsData);
            }
        }

        /** @var \Magento\Backend\Model\Session $backendSession */
        $backendSession = $this->_objectManager->get('Magento\Backend\Model\Session');
        try {
            $this->_objectManager->create('Magento\CurrencySymbol\Model\System\Currencysymbol')
                ->setCurrencySymbolsData($symbolsDataArray);
            $backendSession->addSuccess(__('The custom currency symbols were applied.'));
        } catch (\Exception $e) {
            $backendSession->addError($e->getMessage());
        }

        $this->_redirectReferer();
    }

    /**
     * Resets custom Currency symbol for all store views, websites and default value
     */
    public function resetAction()
    {
        $this->_objectManager->create('Magento\CurrencySymbol\Model\System\Currencysymbol')->resetValues();
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

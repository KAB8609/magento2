<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create totals block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Sales_Order_Create_Totals extends Magento_Adminhtml_Block_Sales_Order_Create_Abstract
{
    protected $_totalRenderers;
    protected $_defaultRenderer = 'Magento_Adminhtml_Block_Sales_Order_Create_Totals_Default';

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_totals');
    }

    public function getTotals()
    {
        return $this->getQuote()->getTotals();
    }

    public function getHeaderText()
    {
        return Mage::helper('Magento_Sales_Helper_Data')->__('Order Totals');
    }

    public function getHeaderCssClass()
    {
        return 'head-money';
    }

    protected function _getTotalRenderer($code)
    {
        $blockName = $code.'_total_renderer';
        $block = $this->getLayout()->getBlock($blockName);
        if (!$block) {
            $block = $this->_defaultRenderer;
            $config = Mage::getConfig()->getNode("global/sales/quote/totals/{$code}/admin_renderer");
            if ($config) {
                $block = (string) $config;
            }

            $block = $this->getLayout()->createBlock($block, $blockName);
        }
        /**
         * Transfer totals to renderer
         */
        $block->setTotals($this->getTotals());
        return $block;
    }

    public function renderTotal($total, $area = null, $colspan = 1)
    {
        return $this->_getTotalRenderer($total->getCode())
            ->setTotal($total)
            ->setColspan($colspan)
            ->setRenderingArea(is_null($area) ? -1 : $area)
            ->toHtml();
    }

    public function renderTotals($area = null, $colspan = 1)
    {
        $html = '';
        foreach($this->getTotals() as $total) {
            if ($total->getArea() != $area && $area != -1) {
                continue;
            }
            $html .= $this->renderTotal($total, $area, $colspan);
        }
        return $html;
    }

    public function canSendNewOrderConfirmationEmail()
    {
        return Mage::helper('Magento_Sales_Helper_Data')->canSendNewOrderConfirmationEmail($this->getQuote()->getStoreId());
    }
}

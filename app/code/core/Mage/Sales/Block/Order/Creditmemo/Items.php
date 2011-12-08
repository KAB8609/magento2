<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales order view items block
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Order_Creditmemo_Items extends Mage_Sales_Block_Items_Abstract
{
    /**
     * Retrieve current order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function getPrintCreditmemoUrl($creditmemo)
    {
        return Mage::getUrl('*/*/printCreditmemo', array('creditmemo_id' => $creditmemo->getId()));
    }

    public function getPrintAllCreditmemosUrl($order)
    {
        return Mage::getUrl('*/*/printCreditmemo', array('order_id' => $order->getId()));
    }

    /**
     * Get creditmemo totals block html
     *
     * @param   Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return  string
     */
    public function getTotalsHtml($creditmemo)
    {
        $totals = $this->getChild('creditmemo_totals');
        $html = '';
        if ($totals) {
            $totals->setCreditmemo($creditmemo);
            $html = $totals->toHtml();
        }
        return $html;
    }

    /**
     * Get html of creditmemo comments block
     *
     * @param   Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return  string
     */
    public function getCommentsHtml($creditmemo)
    {
        $html = '';
        $comments = $this->getChild('creditmemo_comments');
        if ($comments) {
            $comments->setEntity($creditmemo)
                ->setTitle(Mage::helper('Mage_Sales_Helper_Data')->__('About Your Refund'));
            $html = $comments->toHtml();
        }
        return $html;
    }
}

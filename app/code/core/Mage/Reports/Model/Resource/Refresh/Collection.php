<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Grid
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 *  Refresh Statistic Grid collection
 *
 * @category    Mage
 * @package     Mage_Report
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Refresh_Collection extends Varien_Data_Collection
{
    protected function _getUpdatedAt($reportCode)
    {
        $flag = Mage::getModel('Mage_Reports_Model_Flag')->setReportFlagCode($reportCode)->loadSelf();
        return ($flag->hasData())
            ? Mage::app()->getLocale()->storeDate(
                0, new Zend_Date($flag->getLastUpdate(), Varien_Date::DATETIME_INTERNAL_FORMAT), true
            )
            : '';
    }

    /**
     * Load data
     * @return Mage_Reports_Model_Resource_Refresh_Collection|Varien_Data_Collection
     */
    public function loadData( )
    {
        if (!count($this->_items)) {
            $data = array(
                array(
                    'id'            => 'sales',
                    'report'        => Mage::helper('Mage_Sales_Helper_Data')->__('Orders'),
                    'comment'       => Mage::helper('Mage_Sales_Helper_Data')->__('Total Ordered Report'),
                    'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE)
                ),
                array(
                    'id'            => 'tax',
                    'report'        => Mage::helper('Mage_Sales_Helper_Data')->__('Tax'),
                    'comment'       => Mage::helper('Mage_Sales_Helper_Data')->__('Order Taxes Report Grouped by Tax Rates'),
                    'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_TAX_FLAG_CODE)
                ),
                array(
                    'id'            => 'shipping',
                    'report'        => Mage::helper('Mage_Sales_Helper_Data')->__('Shipping'),
                    'comment'       => Mage::helper('Mage_Sales_Helper_Data')->__('Total Shipped Report'),
                    'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_SHIPPING_FLAG_CODE)
                ),
                array(
                    'id'            => 'invoiced',
                    'report'        => Mage::helper('Mage_Sales_Helper_Data')->__('Total Invoiced'),
                    'comment'       => Mage::helper('Mage_Sales_Helper_Data')->__('Total Invoiced VS Paid Report'),
                    'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_INVOICE_FLAG_CODE)
                ),
                array(
                    'id'            => 'refunded',
                    'report'        => Mage::helper('Mage_Sales_Helper_Data')->__('Total Refunded'),
                    'comment'       => Mage::helper('Mage_Sales_Helper_Data')->__('Total Refunded Report'),
                    'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_REFUNDED_FLAG_CODE)
                ),
                array(
                    'id'            => 'coupons',
                    'report'        => Mage::helper('Mage_Sales_Helper_Data')->__('Coupons'),
                    'comment'       => Mage::helper('Mage_Sales_Helper_Data')->__('Promotion Coupons Usage Report'),
                    'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_COUPONS_FLAG_CODE)
                ),
                array(
                    'id'            => 'bestsellers',
                    'report'        => Mage::helper('Mage_Sales_Helper_Data')->__('Bestsellers'),
                    'comment'       => Mage::helper('Mage_Sales_Helper_Data')->__('Products Bestsellers Report'),
                    'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_BESTSELLERS_FLAG_CODE)
                ),
                array(
                    'id'            => 'viewed',
                    'report'        => Mage::helper('Mage_Sales_Helper_Data')->__('Most Viewed'),
                    'comment'       => Mage::helper('Mage_Sales_Helper_Data')->__('Most Viewed Products Report'),
                    'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_PRODUCT_VIEWED_FLAG_CODE)
                ),
            );

            foreach ($data as $value) {
                $item = new Varien_Object();
                $item->setData($value);
                $this->addItem($item);
            }
        }
        return $this;
    }
}
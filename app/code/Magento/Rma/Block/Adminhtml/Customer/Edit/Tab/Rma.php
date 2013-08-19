<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order RMA Grid
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Adminhtml_Customer_Edit_Tab_Rma
    extends Magento_Rma_Block_Adminhtml_Rma_Grid
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    public function _construct()
    {
        parent::_construct();
        $this->setId('customer_edit_tab_rma');
        $this->setUseAjax(true);
    }

    /**
     * Prepare massaction
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Grid
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Configuring and setting collection
     *
     * @return Magento_Rma_Block_Adminhtml_Customer_Edit_Tab_Rma
     */
    protected function _beforePrepareCollection()
    {
        $customerId = null;

        if (Mage::registry('current_customer') && Mage::registry('current_customer')->getId()) {
            $customerId = Mage::registry('current_customer')->getId();
        } elseif ($this->getCustomerId())  {
            $customerId = $this->getCustomerId();
        }
        if ($customerId) {
            /** @var $collection Magento_Rma_Model_Resource_Rma_Grid_Collection */
            $collection = Mage::getResourceModel('Magento_Rma_Model_Resource_Rma_Grid_Collection')
                ->addFieldToFilter('customer_id', $customerId);

            $this->setCollection($collection);
        }
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
    }

    /**
     * Get Url to action
     *
     * @param  string $action action Url part
     * @return string
     */
    protected function _getControllerUrl($action = '')
    {
        return '*/rma/' . $action;
    }

    /**
     * Get Url to action to reload grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/rma/rmaCustomer', array('_current' => true));
    }

    /**
     * Retrieve order model instance
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * ######################## TAB settings #################################
     */
    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Magento_Rma_Helper_Data')->__('Returns');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('Magento_Rma_Helper_Data')->__('Returns');
    }

    /**
     * Check if can show tab
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $customer = Mage::registry('current_customer');
        return (bool)$customer->getId();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}

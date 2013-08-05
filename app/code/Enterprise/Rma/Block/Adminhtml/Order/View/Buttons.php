<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Additional buttons on order view page
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Order_View_Buttons extends Mage_Adminhtml_Block_Sales_Order_View
{
    const CREATE_RMA_BUTTON_DEFAULT_SORT_ORDER = 35;

    /**
     * Add button to Shopping Cart Management etc.
     *
     * @return Enterprise_Rma_Block_Adminhtml_Order_View_Buttons
     */
    public function addButtons()
    {
        if ($this->_isCreateRmaButtonRequired()) {
            $parentBlock = $this->getParentBlock();
            $buttonUrl = $this->_urlBuilder->getUrl('*/rma/new', array('order_id' => $parentBlock->getOrderId()));
            $parentBlock->addButton('create_rma', array(
                'label' => __('Create Returns'),
                'onclick' => 'setLocation(\'' . $buttonUrl . '\')',
            ), 0, $this->_getCreateRmaButtonSortOrder());
        }
        return $this;
    }

    /**
     * Check if 'Create RMA' button has to be displayed
     *
     * @return boolean
     */
    protected function _isCreateRmaButtonRequired()
    {
        $parentBlock = $this->getParentBlock();
        return $parentBlock instanceof Mage_Backend_Block_Template
            && $parentBlock->getOrderId()
            && Mage::helper('Enterprise_Rma_Helper_Data')->canCreateRma($parentBlock->getOrder(), true);
    }

    /**
     * Retrieve sort order of 'Create RMA' button
     *
     * @return int
     */
    protected function _getCreateRmaButtonSortOrder()
    {
        $sortOrder = self::CREATE_RMA_BUTTON_DEFAULT_SORT_ORDER;
        // 'Create RMA' button has to be placed after 'Send Email' button
        if (isset($this->_buttons[0]['send_notification']['sort_order'])) {
            $sortOrder = $this->_buttons[0]['send_notification']['sort_order'] + 5;
        }
        return $sortOrder;
    }
}

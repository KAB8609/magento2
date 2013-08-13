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
 * Adminhtml creditmemo create
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Sales_Order_Creditmemo_Create extends Magento_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order_creditmemo';
        $this->_mode = 'create';

        parent::_construct();

        $this->_removeButton('delete');
        $this->_removeButton('save');

        /*$this->_addButton('submit_creditmemo', array(
            'label'     => Mage::helper('Mage_Sales_Helper_Data')->__('Submit Credit Memo'),
            'class'     => 'save submit-button',
            'onclick'   => '$(\'edit_form\').submit()',
            )
        );*/

    }

    /**
     * Retrieve creditmemo model instance
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return Mage::registry('current_creditmemo');
    }

    public function getHeaderText()
    {
        if ($this->getCreditmemo()->getInvoice()) {
            $header = Mage::helper('Mage_Sales_Helper_Data')->__('New Credit Memo for Invoice #%s', $this->getCreditmemo()->getInvoice()->getIncrementId());
        }
        else {
            $header = Mage::helper('Mage_Sales_Helper_Data')->__('New Credit Memo for Order #%s', $this->getCreditmemo()->getOrder()->getRealOrderId());
        }

        return $header;
    }

    public function getBackUrl()
    {
        return $this->getUrl(
            '*/sales_order/view',
            array('order_id' => $this->getCreditmemo() ? $this->getCreditmemo()->getOrderId() : null)
        );
    }
}

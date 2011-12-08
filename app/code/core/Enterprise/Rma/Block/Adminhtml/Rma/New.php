<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Rma_Block_Adminhtml_Rma_New extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize RMA new page. Set management buttons
     *
     */
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_rma';
        $this->_blockGroup = 'Enterprise_Rma';

        parent::__construct();

        $this->_updateButton('reset', 'label', Mage::helper('Enterprise_Rma_Helper_Data')->__('Cancel'));
        $this->_updateButton('reset', 'class', 'cancel');

        $orderId    = false;
        $link       = $this->getUrl('*/*/');

        if (Mage::registry('current_order') && Mage::registry('current_order')->getId()) {
            $order      = Mage::registry('current_order');
            $orderId    = $order->getId();

            $referer    = $this->getRequest()->getServer('HTTP_REFERER');

            if (strpos($referer, 'customer') !== false) {
                $link = $this->getUrl('*/customer/edit/',
                    array(
                        'id'  => $order->getCustomerId(),
                        'active_tab'=> 'orders'
                    )
                );
            }
        } else {
            return;
        }

        if (Mage::helper('Enterprise_Rma_Helper_Data')->canCreateRma($orderId, true)) {
            $this->_updateButton('reset', 'onclick', "setLocation('" . $link . "')");
            $this->_updateButton('save', 'label', Mage::helper('Enterprise_Rma_Helper_Data')->__('Submit RMA'));
        } else {
            $this->_updateButton('reset', 'onclick', "setLocation('" . $link . "')");
            $this->_removeButton('save');
        }
        $this->_removeButton('back');
    }

    /**
     * Get header text for RMA edit page
     *
     */
    public function getHeaderText()
    {
        return $this->getLayout()->createBlock('Enterprise_Rma_Block_Adminhtml_Rma_Create_Header')->toHtml();
    }

    /**
     * Get form action URL
     *
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save', array('order_id' => Mage::registry('current_order')->getId()));
    }
}

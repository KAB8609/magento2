<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
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
        $this->_blockGroup = 'enterprise_rma';

        parent::__construct();

        $confirm = Mage::helper('enterprise_rma')->__('Are you sure you want to cancel this RMA?');
        $this->_updateButton('reset', 'label', Mage::helper('enterprise_rma')->__('Cancel'));
        $this->_updateButton('reset', 'class', 'cancel');

        $orderId = false;
        if (Mage::registry('current_order') && Mage::registry('current_order')->getId()) {
            $orderId = Mage::registry('current_order')->getId();
        }

        $referer = $this->getRequest()->getServer('HTTP_REFERER');

        $link = $this->getUrl('*/*/');
        if (stristr($referer, 'customer')) {
            $orderId    = $this->getRequest()->getParam('order_id');
            $order      = Mage::getModel('sales/order')->load($orderId);
            if ($order->getId()) {
                $link = $this->getUrl('*/customer/edit/',
                    array(
                        'id'  => $order->getCustomerId(),
                        'active_tab'=> 'orders'
                    )
                );
            }
        }

        if (Mage::helper('enterprise_rma')->canCreateRma($orderId, true)) {
            $this->_updateButton('reset', 'onclick', "setLocation('" . $link . "')");
            $this->_updateButton('save', 'label', Mage::helper('enterprise_rma')->__('Submit RMA'));
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
        return $this->getLayout()->createBlock('enterprise_rma/adminhtml_rma_create_header')->toHtml();
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

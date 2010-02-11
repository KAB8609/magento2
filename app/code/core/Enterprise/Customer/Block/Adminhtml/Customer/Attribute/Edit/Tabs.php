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
 * @package     Enterprise_Customer
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Customer attributes edit page tabs
 *
 * @category   Enterprise
 * @package    Enterprise_Customer
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Attribute_Edit_Tabs extends Enterprise_Enterprise_Block_Adminhtml_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('enterprise_customer')->__('Attribute Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main', array(
            'label'     => Mage::helper('enterprise_customer')->__('Properties'),
            'title'     => Mage::helper('enterprise_customer')->__('Properties'),
            'content'   => $this->getLayout()->createBlock('enterprise_customer/adminhtml_customer_attribute_edit_tab_main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('labels', array(
            'label'     => Mage::helper('enterprise_customer')->__('Manage Label / Options'),
            'title'     => Mage::helper('enterprise_customer')->__('Manage Label / Options'),
            'content'   => $this->getLayout()->createBlock('enterprise_customer/adminhtml_customer_attribute_edit_tab_options')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}

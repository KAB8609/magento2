<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category   Enterprise
 * @package    Enterprise_CustomerBalance
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_History extends Mage_Adminhtml_Block_Template
{
    protected $_collection;

    public function __construct()
    {
        $this->setTemplate('enterprise/customerbalance/balance/history.phtml');
    }

    public function getActionsCollection()
    {
        if( !$this->_collection ) {
            $this->_collection = Mage::getModel('enterprise_customerbalance/balance_history')
                ->getCollection()
                ->addWebsiteData()
                ->addFieldToFilter('customer_id', $this->getRequest()->getParam('id'));
            if( !Mage::helper('enterprise_permissions')->isSuperAdmin() ) {
                if( (bool) Mage::getStoreConfig('customer/account_share/scope') ) {
                    $customer = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id'));
                    $this->_collection->addWebsiteFilter($customer->getWebsiteId());
                } else {
                    $this->_collection->addWebsiteFilter(Mage::helper('enterprise_permissions')->getRelevantWebsites());
                }
            }

        }

        return $this->_collection;
    }
}
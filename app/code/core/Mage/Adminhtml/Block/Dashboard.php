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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Adminhtml_Block_Dashboard extends Mage_Adminhtml_Block_Template
{
    protected $_locale;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('dashboard/index.phtml');

    }

    protected function _prepareLayout()
    {
        $this->setChild('store_switcher',
            $this->getLayout()->createBlock('adminhtml/store_switcher')
                ->setUseConfirm(false)
                ->setSwitchUrl($this->getUrl('*/*/*', array('store'=>null)))
                ->setTemplate('dashboard/store/switcher.phtml')
        );

        //$this->setChild('orders',
        //        $this->getLayout()->createBlock('adminhtml/store_switcher')
        //);
        //$this->setChild('amounts',
        //        $this->getLayout()->createBlock('adminhtml/store_switcher')
        //);

        $this->setChild('lastOrders',
                $this->getLayout()->createBlock('adminhtml/dashboard_orders_grid')
        );

        $this->setChild('totals',
                $this->getLayout()->createBlock('adminhtml/dashboard_totals')
        );

        $this->setChild('sales',
                $this->getLayout()->createBlock('adminhtml/dashboard_sales')
        );

        $this->setChild('customers',
                $this->getLayout()->createBlock('adminhtml/dashboard_customers_grid')
        );

        parent::_prepareLayout();
    }

    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    /**
     * Retrieve locale
     *
     * @return Mage_Core_Model_Locale
     */
    public function getLocale()
    {
        if (!$this->_locale) {
            $this->_locale = Mage::app()->getLocale();
        }
        return $this->_locale;
    }

    /**
     * Retrieve locale code
     *
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->getLocale()->getLocaleCode();
    }

}

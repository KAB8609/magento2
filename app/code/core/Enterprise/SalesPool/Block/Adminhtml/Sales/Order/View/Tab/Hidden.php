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
 * @package     Enterprise_SalesPool
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Sales pool order view hidden tab
 *
 */
class Enterprise_SalesPool_Block_Adminhtml_Sales_Order_View_Tab_Hidden extends Enterprise_Enterprise_Block_Core_Abstract implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Retrieve tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return '';
    }

    /**
     * Retrieve tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Retrieve tab visibility flag
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return false;
    }

    /**
     * Retrieve tab hidden flag
     *
     * @return boolean
     */
    public function isHidden()
    {
        return true;
    }
}

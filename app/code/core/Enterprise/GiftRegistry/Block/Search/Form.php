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
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Gift registry search form
 *
 * @category   Enterprise
 * @package    Enterprise_GiftRegistry
 */
class Enterprise_GiftRegistry_Block_Search_Form extends Enterprise_Enterprise_Block_Core_Template
{
    /**
     * Retrieve form header
     *
     * @return string
     */
    public function getFormHeader()
    {
        return Mage::helper('enterprise_giftregistry')->__('Gift Registry Search');
    }

    /**
     * Return available gift registry types collection
     *
     * @return Enterprise_GiftRegistry_Model_Mysql4_Type_Collection
     */
    public function getTypesCollection()
    {
        return Mage::getModel('enterprise_giftregistry/type')->getCollection()
            ->addStoreData(Mage::app()->getStore()->getId());
    }

    /**
     * Select element for choosing registry type
     *
     * @return array
     */
    public function getTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setData(array(
                'id'    => 'search_type',
                'class' => 'select required-entry'
            ))
            ->setName('search[type]')
            ->setOptions($this->getTypesCollection()->toOptionArray());
        return $select->getHtml();
    }

    /**
     * Return search form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('giftregistry/search/index');
    }

    /**
     * Return search form action url
     *
     * @return string
     */
    public function getAdvancedUrl()
    {
        return $this->getUrl('giftregistry/search/advanced');
    }
}

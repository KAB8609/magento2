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
 * @package     Enterprise_Checkout
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Accordion grid for catalog salable products
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_Products
    extends Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_Abstract
{
    /**
     * Block initializing, grid parameters
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('source_products');
        $this->setDefaultSort('entity_id');
        $this->setPagerVisibility(true);
        $this->setFilterVisibility(true);
        $this->setSaveParametersInSession(true);
        $this->setHeaderText(Mage::helper('enterprise_checkout')->__('Products'));
    }

    /**
     * Return items collection
     *
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $collection = Mage::getModel('catalog/product')->getCollection()
                ->setStore($this->_getStore())
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('sku')
                ->addAttributeToSelect('price')
                ->addAttributeToFilter('type_id',
                    array_keys(Mage::getConfig()->getNode('adminhtml/sales/order/create/available_product_types')->asArray())
                )
                ->addStoreFilter($this->_getStore());
            Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
            $this->setData('items_collection', $collection);
        }
        return $this->getData('items_collection');
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('enterprise_checkout')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('enterprise_checkout')->__('Product Name'),
            'index'     => 'name'
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('enterprise_checkout')->__('SKU'),
            'width'     => '80',
            'index'     => 'sku'
        ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('enterprise_checkout')->__('Price'),
            'type'      => 'price',
            'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
            'index'     => 'price'
        ));

        $this->_addControlColumns();

        return $this;
    }

    /**
     * Return grid URL for sorting and filtering
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/products', array('_current'=>true));
    }
}
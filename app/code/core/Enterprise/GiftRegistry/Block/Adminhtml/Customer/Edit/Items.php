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
 * Adminhtml customer view gift registry items block
 */
class Enterprise_GiftRegistry_Block_Adminhtml_Customer_Edit_Items
    extends Enterprise_Enterprise_Block_Adminhtml_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('giftregistry_customer_items_grid');
        $this->setSortable(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('enterprise_giftregistry/item')->getCollection()
            ->addRegistryFilter($this->getEntity()->getId())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
            'header' => Mage::helper('enterprise_giftregistry')->__('Product ID'),
            'index'  => 'product_id',
            'type'   => 'number',
            'width'  => '120px'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('enterprise_giftregistry')->__('Product Name'),
            'index'  => 'name'
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('enterprise_giftregistry')->__('Product SKU'),
            'index'  => 'sku',
            'width'  => '200px'
        ));

        $this->addColumn('price', array(
            'header' => Mage::helper('enterprise_giftregistry')->__('Price'),
            'index'  => 'price',
            'type'  => 'currency',
            'width' => '120px',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('qty', array(
            'header' => Mage::helper('enterprise_giftregistry')->__('Desired Quantity'),
            'index'  => 'qty',
            'type'   => 'number',
            'width'  => '120px'
        ));

        $this->addColumn('qty_fulfilled', array(
            'header' => Mage::helper('enterprise_giftregistry')->__('Fulfilled Quantity'),
            'index'  => 'qty_fulfilled',
            'type'   => 'number',
            'width'  => '120px'
        ));

        $this->addColumn('notes', array(
            'header' => Mage::helper('enterprise_giftregistry')->__('Notes'),
            'index'  => 'notes',
            'width'  => '120px'
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product/edit', array('id' => $row->getProductId()));
    }

    public function getEntity()
    {
        return Mage::registry('current_giftregistry_entity');
    }
}


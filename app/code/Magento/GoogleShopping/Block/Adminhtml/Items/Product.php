<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Products Grid to add to Google Content
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleShopping\Block\Adminhtml\Items;

class Product extends \Magento\Adminhtml\Block\Widget\Grid
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('googleshopping_selection_search_grid');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return \Magento\GoogleShopping\Block\Adminhtml\Items\Product
     */
    protected function _beforeToHtml()
    {
        $this->setId($this->getId().'_'.$this->getIndex());
        $this->getChildBlock('reset_filter_button')->setData('onclick', $this->getJsObjectName().'.resetFilter()');
        $this->getChildBlock('search_button')->setData('onclick', $this->getJsObjectName().'.doFilter()');
        return parent::_beforeToHtml();
    }

    /**
     * Prepare grid collection object
     *
     * @return \Magento\GoogleShopping\Block\Adminhtml\Items\Product
     */
    protected function _prepareCollection()
    {
        $collection = \Mage::getModel('Magento\Catalog\Model\Product')->getCollection()
            ->setStore($this->_getStore())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('attribute_set_id');

        $store = $this->_getStore();
        if ($store->getId()) {
            $collection->addStoreFilter($store);
        }

        $excludeIds = $this->_getGoogleShoppingProductIds();
        if ($excludeIds) {
            $collection->addIdFilter($excludeIds, true);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return \Magento\GoogleShopping\Block\Adminhtml\Items\Product
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => __('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => __('Product'),
            'index'     => 'name',
            'column_css_class'=> 'name'
        ));

        $sets = \Mage::getResourceModel('Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection')
            ->setEntityTypeFilter(\Mage::getModel('Magento\Catalog\Model\Product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('type',
            array(
                'header'=> __('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => \Mage::getSingleton('Magento\Catalog\Model\Product\Type')->getOptionArray(),
        ));

        $this->addColumn('set_name',
            array(
                'header'=> __('Attribute Set'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

        $this->addColumn('sku', array(
            'header'    => __('SKU'),
            'width'     => '80px',
            'index'     => 'sku',
            'column_css_class'=> 'sku'
        ));
        $this->addColumn('price', array(
            'header'    => __('Price'),
            'align'     => 'center',
            'type'      => 'currency',
            'currency_code' => $this->_getStore()->getDefaultCurrencyCode(),
            'rate'      => $this->_getStore()->getBaseCurrency()->getRate($this->_getStore()->getDefaultCurrencyCode()),
            'index'     => 'price'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid massaction actions
     *
     * @return \Magento\GoogleShopping\Block\Adminhtml\Items\Product
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('add', array(
             'label'    => __('Add to Google Content'),
             'url'      => $this->getUrl('*/*/massAdd', array('_current'=>true)),
        ));
        return $this;
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/googleshopping_selection/grid', array('index' => $this->getIndex(),'_current'=>true));
    }

    /**
     * Get array with product ids, which was exported to Google Content
     *
     * @return array
     */
    protected function _getGoogleShoppingProductIds()
    {
        $collection = \Mage::getResourceModel('Magento\GoogleShopping\Model\Resource\Item\Collection')
            ->addStoreFilter($this->_getStore()->getId())
            ->load();
        $productIds = array();
        foreach ($collection as $item) {
            $productIds[] = $item->getProductId();
        }
        return $productIds;
    }

    /**
     * Get store model by request param
     *
     * @return \Magento\Core\Model\Store
     */
    protected function _getStore()
    {
        return \Mage::app()->getStore($this->getRequest()->getParam('store'));
    }
}

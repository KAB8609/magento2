<?php
/**
 * Customer's tags grid
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Customer_Edit_Tab_Tag extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
    }

    protected function _prepareCollection()
    {
        $tagId = Mage::registry('tagId');

        $collection = Mage::getResourceModel('tag/customer_collection')
            ->addCustomerFilter($this->getCustomerId());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
            $this->getCollection()->addProductName();
        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => __('Tag Name'),
            'index'     => 'name',
        ));

        $this->addColumn('status', array(
            'header'    => __('Status'),
            'width'     => '90px',
            'index'     => 'status',
            'type'      => 'options',
            'options'    => array(
                Mage_Tag_Model_Tag::STATUS_DISABLED => __('Disabled'),
                Mage_Tag_Model_Tag::STATUS_PENDING  => __('Pending'),
                Mage_Tag_Model_Tag::STATUS_APPROVED => __('Approved'),
            ),
            'filter'    => false,
        ));

        $this->addColumn('product', array(
            'header'    => __('Product Name'),
            'index'     => 'product',
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('product_sku', array(
            'header'    => __('SKU'),
            'index'     => 'product_sku',
            'filter'    => false,
            'sortable'  => false,
        ));

        return parent::_prepareColumns();
    }

    protected function getRowUrl($row)
    {
        return Mage::getUrl('*/tag/edit', array(
            'tag_id' => $row->getId(),
            'customer_id' => $this->getCustomerId(),
        ));
    }

    public function getGridUrl()
    {
        return Mage::getUrl('*/customer/tagGrid', array(
            '_current' => true,
            'id'       => $this->getCustomerId()
        ));
    }
}
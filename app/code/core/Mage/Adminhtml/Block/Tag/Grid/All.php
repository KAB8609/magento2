<?php
/**
 * Adminhtml all tags grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Tag_Grid_All extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tagsGrid');
//        $this->setDefaultSort('created_at', 'desc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tag/tag_collection')
//            ->addStoreFilter(Mage::getSingleton('core/store')->getId())
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('tagname', array(
            'header'    => __('Tag'),
            'index'     => 'tagname',
        ));
        $this->addColumn('total_used', array(
            'header'    => __('# of Uses'),
            'width'     => '140px',
            'index'     => 'total_used',
            'type'      => 'number',
        ));
        $this->addColumn('status', array(
            'header'    => __('Approved'),
            'width'     => '40px',
            'index'     => 'status',
            'type'      => 'yesno',
        ));
        $this->addColumn('action', array(
            'header'    => __('Action'),
            'align'     => 'center',
            'width'     => '140px',
            'format'    => '<a href="'.Mage::getUrl('*/*/edit/id/$tag_id').'">'.__('Edit').'</a>'
                . '&nbsp;&nbsp; <a href="'.Mage::getUrl('*/*/products/tag_id/$tag_id').'">'.__('View Products').'</a>',
            'index'     => 'tag_id',
            'sortable'  => false,
            'filter'    => false,
        ));

        $this->setColumnFilter('id')
            ->setColumnFilter('tagname')
            ->setColumnFilter('total_used')
        ;

        return parent::_prepareColumns();
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection() && $column->getFilter()->getValue()) {
            $this->getCollection()->addAttributeToFilter($column->getIndex(), $column->getFilter()->getCondition());
        }
        return $this;
    }
}
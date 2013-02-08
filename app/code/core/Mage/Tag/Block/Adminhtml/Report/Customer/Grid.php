<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml tags by customers report grid block
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Block_Adminhtml_Report_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('grid');
    }

    protected function _prepareCollection()
    {

        $collection = Mage::getResourceModel('Mage_Tag_Model_Resource_Reports_Customer_Collection');

        $collection->addStatusFilter(Mage_Tag_Model_Tag::STATUS_APPROVED)
            ->addGroupByCustomer()
            ->addTagedCount();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('entity_id', array(
            'header'            => Mage::helper('Mage_Tag_Helper_Data')->__('ID'),
            'index'             => 'entity_id',
            'header_css_class'  => 'col-id',
            'column_css_class'  => 'col-id'
        ));

        $this->addColumn('firstname', array(
            'header'            => Mage::helper('Mage_Tag_Helper_Data')->__('First Name'),
            'index'             => 'firstname',
            'header_css_class'  => 'col-first-name',
            'column_css_class'  => 'col-first-name'
        ));

        $this->addColumn('lastname', array(
            'header'            => Mage::helper('Mage_Tag_Helper_Data')->__('Last Name'),
            'index'             => 'lastname',
            'header_css_class'  => 'col-last-name',
            'column_css_class'  => 'col-last-name'
        ));

        $this->addColumn('taged', array(
            'header'            => Mage::helper('Mage_Tag_Helper_Data')->__('Total Tags'),
            'index'             => 'taged',
            'header_css_class'  => 'col-qty',
            'column_css_class'  => 'col-qty',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('Mage_Tag_Helper_Data')->__('Action'),
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('Mage_Tag_Helper_Data')->__('Show Tags'),
                        'url'     => array(
                            'base'=>'*/*/customerDetail'
                        ),
                        'field'   => 'id'
                    )
                ),
                'is_system' => true,
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'header_css_class'  => 'col-actions',
                'column_css_class'  => 'col-actions'
        ));

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportCustomerCsv', Mage::helper('Mage_Tag_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportCustomerExcel', Mage::helper('Mage_Tag_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/customerDetail', array('id'=>$row->getId()));
    }

}


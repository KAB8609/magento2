<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * description
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Poll_Grid extends Magento_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('pollGrid');
        $this->setDefaultSort('poll_title');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Magento_Poll_Model_Poll')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();

        if (!Mage::app()->isSingleStoreMode()) {
            $this->getCollection()->addStoreData();
        }

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('poll_id', array(
            'header'    => Mage::helper('Magento_Poll_Helper_Data')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'poll_id',
        ));

        $this->addColumn('poll_title', array(
            'header'    => Mage::helper('Magento_Poll_Helper_Data')->__('Poll Question'),
            'align'     =>'left',
            'index'     => 'poll_title',
        ));

        $this->addColumn('votes_count', array(
            'header'    => Mage::helper('Magento_Poll_Helper_Data')->__('Responses'),
            'width'     => '50px',
            'type'      => 'number',
            'index'     => 'votes_count',
        ));

        $this->addColumn('date_posted', array(
            'header'    => Mage::helper('Magento_Poll_Helper_Data')->__('Posted'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'datetime',
            'index'     => 'date_posted',
            'date_format' => Mage::app()->getLocale()->getDateFormat()
        ));

        $this->addColumn('date_closed', array(
            'header'    => Mage::helper('Magento_Poll_Helper_Data')->__('Closed'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'datetime',
            'default'   => '--',
            'index'     => 'date_closed',
            'date_format' => Mage::app()->getLocale()->getDateFormat()
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible_in', array(
                'header'    => Mage::helper('Mage_Review_Helper_Data')->__('Visibility'),
                'index'     => 'stores',
                'type'      => 'store',
                'store_view' => true,
                'sortable'   => false,
            ));
        }

        /*
        $this->addColumn('active', array(
            'header'    => Mage::helper('Magento_Poll_Helper_Data')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'active',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));
        */
        $this->addColumn('closed', array(
            'header'    => Mage::helper('Magento_Poll_Helper_Data')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'closed',
            'type'      => 'options',
            'options'   => array(
                1 => Mage::helper('Magento_Poll_Helper_Data')->__('Closed'),
                0 => Mage::helper('Magento_Poll_Helper_Data')->__('Open')
            ),
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}

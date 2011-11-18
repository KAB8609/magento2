<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customer orders grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Tags extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('ordersGrid');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Mage_Customer_Model_Resource_Customer_Collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('Mage_Customer_Helper_Data')->__('ID'),
            'width'     =>5,
            'align'     =>'center',
            'sortable'  =>true,
            'index'     =>'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('Mage_Customer_Helper_Data')->__('Name'),
            'index'     =>'name'
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('Mage_Customer_Helper_Data')->__('Email'),
            'width'     =>40,
            'align'     =>'center',
            'index'     =>'email'
        ));
        $this->addColumn('telephone', array(
            'header'    => Mage::helper('Mage_Customer_Helper_Data')->__('Telephone'),
            'align'     =>'center',
            'index'     =>'billing_telephone'
        ));
        $this->addColumn('billing_postcode', array(
            'header'    => Mage::helper('Mage_Customer_Helper_Data')->__('ZIP/Post Code'),
            'index'     =>'billing_postcode',
        ));
        $this->addColumn('billing_country_id', array(
            'header'    => Mage::helper('Mage_Customer_Helper_Data')->__('Country'),
            'type'      => 'country',
            'index'     => 'billing_country_id',
        ));
        $this->addColumn('customer_since', array(
            'header'    => Mage::helper('Mage_Customer_Helper_Data')->__('Customer Since'),
            'type'      => 'date',
            'format'    => 'Y.m.d',
            'index'     =>'created_at',
        ));
        $this->addColumn('action', array(
            'header'    => Mage::helper('Mage_Customer_Helper_Data')->__('Action'),
            'align'     =>'center',
            'format'    =>'<a href="'.$this->getUrl('*/sales/edit/id/$entity_id').'">'.Mage::helper('Mage_Customer_Helper_Data')->__('Edit').'</a>',
            'filter'    =>false,
            'sortable'  =>false,
            'is_system' =>true
        ));

        $this->setColumnFilter('entity_id')
            ->setColumnFilter('email')
            ->setColumnFilter('name');

        $this->addExportType('*/*/exportCsv', Mage::helper('Mage_Customer_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('Mage_Customer_Helper_Data')->__('Excel XML'));
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/index', array('_current'=>true));
    }

}

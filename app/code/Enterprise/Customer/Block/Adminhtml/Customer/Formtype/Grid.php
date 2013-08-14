<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Form Types Grid Block
 *
 * @category   Enterprise
 * @package    Enterprise_Customer
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Formtype_Grid extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize Grid Block
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('code');
        $this->setDefaultDir('asc');
    }

    /**
     * Prepare grid collection object
     *
     * @return Enterprise_Customer_Block_Adminhtml_Customer_Formtype_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Magento_Eav_Model_Form_Type')
            ->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return Enterprise_Customer_Block_Adminhtml_Customer_Formtype_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('code', array(
            'header'    => Mage::helper('Enterprise_Customer_Helper_Data')->__('Type Code'),
            'index'     => 'code',
        ));

        $this->addColumn('label', array(
            'header'    => Mage::helper('Enterprise_Customer_Helper_Data')->__('Label'),
            'index'     => 'label',
        ));

        $this->addColumn('store_id', array(
            'header'    => Mage::helper('Enterprise_Customer_Helper_Data')->__('Store View'),
            'index'     => 'store_id',
            'type'      => 'store'
        ));

        /** @var $label Magento_Core_Model_Theme_Label */
        $label = Mage::getModel('Magento_Core_Model_Theme_Label');
        $design = $label->getLabelsCollection();
        array_unshift($design, array(
            'value' => 'all',
            'label' => Mage::helper('Enterprise_Customer_Helper_Data')->__('All Themes')
        ));
        $this->addColumn('theme', array(
            'header'     => Mage::helper('Enterprise_Customer_Helper_Data')->__('Theme'),
            'type'       => 'theme',
            'index'      => 'theme',
            'options'    => $design,
            'with_empty' => true,
            'default'    => Mage::helper('Enterprise_Customer_Helper_Data')->__('All Themes')
        ));

        $this->addColumn('is_system', array(
            'header'    => Mage::helper('Enterprise_Customer_Helper_Data')->__('System'),
            'index'     => 'is_system',
            'type'      => 'options',
            'options'   => array(
                0 => Mage::helper('Enterprise_Customer_Helper_Data')->__('No'),
                1 => Mage::helper('Enterprise_Customer_Helper_Data')->__('Yes'),
            )
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve row click URL
     *
     * @param Magento_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('type_id' => $row->getId()));
    }
}

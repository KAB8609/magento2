<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * roles grid
 *
 * @category   Mage
 * @package    Mage_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_User_Block_Role_Grid extends Mage_Backend_Block_Widget_Grid
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('roleGrid');
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('role_id');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection =  Mage::getModel('Mage_User_Model_Role')->getCollection()
            ->setRolesFilter();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('role_id', array(
            'header'    =>Mage::helper('Mage_User_Helper_Data')->__('ID'),
            'index'     =>'role_id',
            'align'     => 'right',
            'width'    => '50px'
        ));

        $this->addColumn('role_name', array(
            'header'    =>Mage::helper('Mage_User_Helper_Data')->__('Role Name'),
            'index'     =>'role_name'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/roleGrid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/editrole', array('rid' => $row->getRoleId()));
    }
}

<?php
/**
 * Adminhtml products report grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */
class Mage_Adminhtml_Block_Report_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('productsReportGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {
       
        $collection = Mage::getResourceModel('reports/product_collection');
        $collection->getEntity()->setStore(0);
        $this->setCollection($collection);
      
        parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    =>__('ID'),
            'width'     =>'50px',
            'index'     =>'entity_id'
        ));
        
        $this->addColumn('name', array(
            'header'    =>__('Name'),
            'index'     =>'name'
        ));    
        
        $this->addColumn('viewed', array(
            'header'    =>__('Number Viewed'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'viewed'
        ));
        
        $this->addColumn('added', array(
            'header'    =>__('Number Added'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'added'
        ));
        
        $this->addColumn('purchased', array(
            'header'    =>__('Number Purchased'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'purchased'
        ));
        
        $this->addColumn('fulfilled', array(
            'header'    =>__('Number Fulfilled'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'fulfilled'
        ));
        
        $this->addColumn('revenue', array(
            'header'    =>__('Revenue'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'revenue'
        ));
       
        return parent::_prepareColumns();
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin RMA create order grid block
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Rma_Block_Adminhtml_Rma_New_Tab_Items_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
//    extends Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid
{
    /**
     * Variable to store store-depended string values of attributes
     *
     * @var null|array
     */
    protected $_attributeOptionValues = null;

    /**
     * Block constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('rma_items_grid');
        $this->setDefaultSort('entity_id');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->_gatherOrderItemsData();
    }

    /**
     * Gather items quantity data from Order item collection
     *
     * @return void
     */
    protected function _gatherOrderItemsData()
    {
        $itemsData = array();
        if (Mage::registry('current_order')) {
            foreach (Mage::registry('current_order')->getItemsCollection() as $item) {
                $itemsData[$item->getId()] = array(
                    'qty_shipped' => $item->getQtyShipped(),
                    'qty_returned' => $item->getQtyReturned()
                );
            }
        }
        $this->setOrderItemsData($itemsData);
    }

    /**
     * Prepare grid collection object
     *
     * @return Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Enterprise_Rma_Model_Resource_Item_Collection */
        $collection = Mage::getResourceModel('Enterprise_Rma_Model_Resource_Item_Collection');
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('entity_id', NULL);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('product_name', array(
            'header'   => Mage::helper('Enterprise_Rma_Helper_Data')->__('Product Name'),
            'width'    => '80px',
            'type'     => 'text',
            'index'    => 'product_name',
            'sortable' => false,
            'escape'   => true,
        ));

        $this->addColumn('product_sku', array(
            'header'   => Mage::helper('Enterprise_Rma_Helper_Data')->__('SKU'),
            'width'    => '80px',
            'type'     => 'text',
            'index'    => 'product_sku',
            'sortable' => false,
            'escape'   => true,
        ));

        //Renderer puts available quantity instead of order_item_id
        $this->addColumn('qty_ordered', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('Remaining Qty'),
            'width' => '80px',
            'getter'   => array($this, 'getQtyOrdered'),
            'type'  => 'text',
            'index' => 'qty_ordered',
            'sortable' => false,
            'order_data' => $this->getOrderItemsData(),
            'renderer'  => 'Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Quantity',
        ));

        $this->addColumn('qty_requested', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('Requested Qty'),
            'width' => '80px',
            'index' => 'qty_requested',
            'type'  => 'input',
            'sortable' => false
        ));

        $eavHelper = Mage::helper('Enterprise_Rma_Helper_Eav');
        $this->addColumn('reason', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('Reason to Return'),
            'width' => '80px',
            'getter'   => array($this, 'getReasonOptionStringValue'),
            'type'  => 'select',
            'options' => array(''=>'') + $eavHelper->getAttributeOptionValues('reason'),
            'index' => 'reason',
            'sortable' => false
        ));

        $this->addColumn('condition', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('Item Condition'),
            'width' => '80px',
            'type'  => 'select',
            'options' => array(''=>'') + $eavHelper->getAttributeOptionValues('condition'),
            'index' => 'condition',
            'sortable' => false
        ));

        $this->addColumn('resolution', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('Resolution'),
            'width' => '80px',
            'index' => 'resolution',
            'type'  => 'select',
            'options' => array(''=>'') + $eavHelper->getAttributeOptionValues('resolution'),
            'sortable' => false
        ));

        $actionsArray = array(
            array(
                'caption'   => Mage::helper('Enterprise_Rma_Helper_Data')->__('Delete'),
                'url'       => array('base'=> '*/*/delete'),
                'field'     => 'id',
                'onclick'  => 'alert(\'Delete\');return false;'
            ),
            array(
                'caption'   => Mage::helper('Enterprise_Rma_Helper_Data')->__('Add Details'),
                'url'       => array('base'=> '*/*/edit'),
                'field'     => 'id',
                'onclick'  => 'alert(\'Details\');return false;'
            ),
        );

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('Enterprise_Rma_Helper_Data')->__('Action'),
                'width'     => '100',
                'renderer'  => 'Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Action',
                'actions'   => $actionsArray,
                'sortable'  => false,
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    /**
     * Get available for return item quantity
     *
     * @param Varien_Object $row
     * @return int
     */
    public function getQtyOrdered($row)
    {
        $orderItemsData = $this->getOrderItemsData();
        if (is_array($orderItemsData)
                && isset($orderItemsData[$row->getOrderItemId()])
                && isset($orderItemsData[$row->getOrderItemId()]['qty_shipped'])
                && isset($orderItemsData[$row->getOrderItemId()]['qty_returned'])) {
            $return = $orderItemsData[$row->getOrderItemId()]['qty_shipped'] -
                    $orderItemsData[$row->getOrderItemId()]['qty_returned'];
        } else {
            $return = 0;
        }
        return $return;
    }

    /**
     * Get string value of "Reason to Return" Attribute
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getReasonOptionStringValue($row)
    {
        return $this->_getAttributeOptionStringValue($row->getReason());
    }

    /**
     * Get string value of "Reason to Return" Attribute
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getResolutionOptionStringValue($row)
    {
        return $this->_getAttributeOptionStringValue($row->getResolution());
    }

    /**
     * Get string value of "Reason to Return" Attribute
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getConditionOptionStringValue($row)
    {
        return $this->_getAttributeOptionStringValue($row->getCondition());
    }

    /**
     * Get string value of "BugsCoverage" Attribute
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getStatusOptionStringValue($row)
    {
        return $row->getStatusLabel();
    }

    /**
     * Get string value option-type attribute by it's unique int value
     *
     * @param int $value
     * @return string
     */
    protected function _getAttributeOptionStringValue($value)
    {
        if (is_null($this->_attributeOptionValues)) {
            $this->_attributeOptionValues = Mage::helper('Enterprise_Rma_Helper_Eav')->getAttributeOptionStringValues();
        }
        if (isset($this->_attributeOptionValues[$value])) {
            return $this->escapeHtml($this->_attributeOptionValues[$value]);
        } else {
            return $this->escapeHtml($value);
        }
    }

    /**
     * Return row url for js event handlers
     *
     * @param Mage_Catalog_Model_Product|Varien_Object
     * @return string
     */
    public function getRowUrl($item)
    {
        //$res = parent::getRowUrl($item);
        return null;
    }
}

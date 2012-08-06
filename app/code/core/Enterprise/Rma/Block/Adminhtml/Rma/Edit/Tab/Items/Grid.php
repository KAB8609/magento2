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

class Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Default limit collection
     *
     * @var int
     */
    protected $_defaultLimit = 0;

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
        $this->setId('enterprise_rma_item_edit_grid');
        $this->setDefaultSort('entity_id');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setSortable(false);
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
        $rma = Mage::registry('current_rma');

        /** @var $collection Enterprise_Rma_Model_Resource_Item_Collection */
        $collection = $rma->getItemsForDisplay();

        if ($this->getItemFilter()) {
            $collection->addFilter('entity_id', $this->getItemFilter());
        }

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
        $statusManager = Mage::getSingleton('Enterprise_Rma_Model_Item_Status');
        $rma = Mage::registry('current_rma');
        if ($rma
            && (($rma->getStatus() === Enterprise_Rma_Model_Rma_Source_Status::STATE_CLOSED)
                || ($rma->getStatus() === Enterprise_Rma_Model_Rma_Source_Status::STATE_PROCESSED_CLOSED))
        ) {
            $statusManager->setOrderIsClosed();
        }

        $this->addColumn('product_admin_name', array(
            'header' => Mage::helper('Enterprise_Rma_Helper_Data')->__('Product Name'),
            'width'  => '80px',
            'type'   => 'text',
            'index'  => 'product_admin_name',
            'escape' => true,
        ));

        $this->addColumn('product_admin_sku', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('SKU'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'product_admin_sku',
        ));

        //Renderer puts available quantity instead of order_item_id
        $this->addColumn('qty_ordered', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('Remaining Qty'),
            'width' => '80px',
            'getter'   => array($this, 'getQtyOrdered'),
            'renderer'  => 'Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Quantity',
            'index' => 'qty_ordered',
            'order_data' => $this->getOrderItemsData(),
        ));

        $this->addColumn('qty_requested', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('Requested Qty'),
            'width' => '80px',
            'index' => 'qty_requested',
            'renderer'  => 'Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textinput',
            'validate_class' => 'validate-greater-than-zero'
        ));

        $this->addColumn('qty_authorized', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('Authorized Qty'),
            'width' => '80px',
            'index' => 'qty_authorized',
            'renderer'  => 'Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textinput',
            'validate_class' => 'validate-greater-than-zero'
        ));

        $this->addColumn('qty_returned', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('Returned Qty'),
            'width' => '80px',
            'index' => 'qty_returned',
            'renderer'  => 'Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textinput',
            'validate_class' => 'validate-greater-than-zero'
        ));

        $this->addColumn('qty_approved', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('Approved Qty'),
            'width' => '80px',
            'index' => 'qty_approved',
            'renderer'  => 'Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textinput',
            'validate_class' => 'validate-greater-than-zero'
        ));

        $this->addColumn('reason', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('Reason to Return'),
            'width' => '80px',
            'getter'   => array($this, 'getReasonOptionStringValue'),
            'renderer'  => 'Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Reasonselect',
            'options' => Mage::helper('Enterprise_Rma_Helper_Eav')->getAttributeOptionValues('reason'),
            'index' => 'reason',
        ));

        $this->addColumn('condition', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('Item Condition'),
            'width' => '80px',
            'getter'   => array($this, 'getConditionOptionStringValue'),
            'renderer'  => 'Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textselect',
            'options' => Mage::helper('Enterprise_Rma_Helper_Eav')->getAttributeOptionValues('condition'),
            'index' => 'condition',
        ));

        $this->addColumn('resolution', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('Resolution'),
            'width' => '80px',
            'index' => 'resolution',
            'getter'   => array($this, 'getResolutionOptionStringValue'),
            'renderer'  => 'Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textselect',
            'options' => Mage::helper('Enterprise_Rma_Helper_Eav')->getAttributeOptionValues('resolution'),
        ));

        $this->addColumn('status', array(
            'header'=> Mage::helper('Enterprise_Rma_Helper_Data')->__('Status'),
            'width' => '80px',
            'index' => 'status',
            'getter'=> array($this, 'getStatusOptionStringValue'),
            'renderer'  => 'Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Status',
        ));

        $actionsArray = array(
            array(
                'caption'   => Mage::helper('Enterprise_Rma_Helper_Data')->__('Details'),
                'class'     => 'item_details',
            ),
        );
        if (!($rma
            && (($rma->getStatus() === Enterprise_Rma_Model_Rma_Source_Status::STATE_CLOSED)
                || ($rma->getStatus() === Enterprise_Rma_Model_Rma_Source_Status::STATE_PROCESSED_CLOSED))
        )) {
                $actionsArray[] = array(
                'caption'   => Mage::helper('Enterprise_Rma_Helper_Data')->__('Split'),
                'class'     => 'item_split_line',
                'status_depended' => '1'
            );
        }

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('Enterprise_Rma_Helper_Data')->__('Action'),
                'width'     => '100',
                'renderer'  => 'Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Action',
                'actions'   => $actionsArray,
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
     * Get string value of "Status" Attribute
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
     * Sets all available fields in editable state
     *
     * @return Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid
     */
    public function setAllFieldsEditable()
    {
        Mage::getSingleton('Enterprise_Rma_Model_Item_Status')->setAllEditable();
        return $this;
    }

}

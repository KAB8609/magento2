<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cart items attributes subselection condition
 */
class Enterprise_Reminder_Model_Rule_Condition_Cart_Attributes
    extends Enterprise_Reminder_Model_Condition_Abstract
{
    protected $_inputType = 'numeric';

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('Enterprise_Reminder_Model_Rule_Condition_Cart_Attributes');
        $this->setValue(null);
    }

    /**
     * Get information for being presented in condition list
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array('value' => $this->getType(),
            'label' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Numeric Attribute'));
    }

    /**
     * Init available options list
     *
     * @return Enterprise_Reminder_Model_Rule_Condition_Cart_Attributes
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'weight' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('weight'),
            'row_weight' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('row weight'),
            'qty' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('quantity'),
            'price' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('base price'),
            'base_cost' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('base cost')
        ));
        return $this;
    }

    /**
     * Condition string on conditions page
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('Enterprise_Reminder_Helper_Data')->__('Item %s %s %s:',
                $this->getAttributeElementHtml(), $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Build condition limitations sql string for specific website
     *
     * @param $customer
     * @param int | Zend_Db_Expr $website
     * @return Varien_Db_Select
     */
    public function getConditionsSql($customer, $website)
    {
        $quoteTable = $this->getResource()->getTable('sales_flat_quote');
        $quoteItemTable = $this->getResource()->getTable('sales_flat_quote_item');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(array('item' => $quoteItemTable), array(new Zend_Db_Expr(1)));

        $select->joinInner(
            array('quote' => $quoteTable),
            'item.quote_id = quote.entity_id',
            array()
        );

        switch ($this->getAttribute()) {
            case 'weight':
                $field = 'item.weight';
                break;
            case 'row_weight':
                $field = 'item.row_weight';
                break;
            case 'qty':
                $field = 'item.qty';
                break;
            case 'price':
                $field = 'item.price';
                break;
            case 'base_cost':
                $field = 'item.base_cost';
                break;
            default:
                Mage::throwException(Mage::helper('Enterprise_Reminder_Helper_Data')->__('Unknown attribute specified'));
        }

        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');
        $select->where('quote.is_active = 1');
        $select->where("{$field} {$operator} ?", $this->getValue());
        $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));
        Mage::getResourceHelper('Enterprise_Reminder')->setRuleLimit($select, 1);
        return $select;
    }
}

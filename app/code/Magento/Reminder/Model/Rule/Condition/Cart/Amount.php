<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cart totals amount condition
 */
class Magento_Reminder_Model_Rule_Condition_Cart_Amount
    extends Magento_Reminder_Model_Condition_Abstract
{
    /**
     * @var string
     */
    protected $_inputType = 'numeric';

    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Magento_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Magento_Reminder_Model_Rule_Condition_Cart_Amount');
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
            'label' => Mage::helper('Magento_Reminder_Helper_Data')->__('Total Amount'));
    }

    /**
     * Init available options list
     *
     * @return Magento_Reminder_Model_Rule_Condition_Cart_Amount
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'subtotal' => Mage::helper('Magento_Reminder_Helper_Data')->__('subtotal'),
            'grand_total' => Mage::helper('Magento_Reminder_Helper_Data')->__('grand total')
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
            . Mage::helper('Magento_Reminder_Helper_Data')->__('Shopping cart %s amount %s %s:', $this->getAttributeElementHtml(), $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Build condition limitations sql string for specific website
     *
     * @param $customer
     * @param int | Zend_Db_Expr $website
     * @return Magento_DB_Select
     */
    public function getConditionsSql($customer, $website)
    {
        $table = $this->getResource()->getTable('sales_flat_quote');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(array('quote' => $table), array(new Zend_Db_Expr(1)));

        switch ($this->getAttribute()) {
            case 'subtotal':
                $field = 'quote.base_subtotal';
                break;
            case 'grand_total':
                $field = 'quote.base_grand_total';
                break;
            default:
                Mage::throwException(
                    Mage::helper('Magento_Reminder_Helper_Data')->__('Unknown quote total specified')
                );
        }

        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');
        $select->where('quote.is_active = 1');
        $select->where("{$field} {$operator} ?", $this->getValue());
        $select->where($this->_createCustomerFilter($customer, 'customer_id'));
        $select->limit(1);
        return $select;
    }
}

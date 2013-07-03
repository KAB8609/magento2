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
 * Customer cart conditions combine
 */
class Enterprise_Reminder_Model_Rule_Condition_Cart
    extends Enterprise_Reminder_Model_Condition_Combine_Abstract
{
    /**
     * @var Mage_Core_Model_Date
     */
    protected $_dateModel;

    /**
     * class constructor
     */
    public function __construct(Mage_Core_Model_Date $dateModel)
    {
        parent::__construct();
        $this->_dateModel = $dateModel;
        $this->setType('Enterprise_Reminder_Model_Rule_Condition_Cart');
        $this->setValue(null);
    }

    /**
     * Get list of available subconditions
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return Mage::getModel('Enterprise_Reminder_Model_Rule_Condition_Cart_Combine')->getNewChildSelectOptions();
    }

    /**
     * Get input type for attribute value
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Override parent method
     *
     * @return Enterprise_Reminder_Model_Rule_Condition_Cart
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array());
        return $this;
    }

    /**
     * Prepare operator select options
     *
     * @return Enterprise_Reminder_Model_Rule_Condition_Cart
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            '==' => Mage::helper('Mage_Rule_Helper_Data')->__('for'),
            '>'  => Mage::helper('Mage_Rule_Helper_Data')->__('for greater than'),
            '>=' => Mage::helper('Mage_Rule_Helper_Data')->__('for or greater than')
        ));
        return $this;
    }

    /**
     * Return required validation
     *
     * @return true
     */
    protected function _getRequiredValidation()
    {
        return true;
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('Enterprise_Reminder_Helper_Data')->__('Shopping cart is not empty and abandoned %s %s days and %s of these conditions match:', $this->getOperatorElementHtml(), $this->getValueElementHtml(), $this->getAggregatorElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Get condition SQL select
     *
     * @param   int|Zend_Db_Expr $customer
     * @param   int|Zend_Db_Expr $website
     * @return  Varien_Db_Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $conditionValue = (int) $this->getValue();
        if ($conditionValue < 0) {
            Mage::throwException(Mage::helper('Enterprise_Reminder_Helper_Data')->__('The root shopping cart condition should have a days value of 0 or greater.'));
        }

        $table = $this->getResource()->getTable('sales_flat_quote');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(array('quote' => $table), array(new Zend_Db_Expr(1)));

        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');

        $currentTime = $this->_dateModel->gmtDate('Y-m-d');
        $daysDiffSql = Mage::getResourceHelper('Enterprise_Reminder')
            ->getDateDiff('quote.updated_at', $select->getAdapter()->formatDate($currentTime));
        if ($operator == '=') {
            $select->where($daysDiffSql . ' < ?', $conditionValue);
            $select->where($daysDiffSql . ' > ?', $conditionValue - 1);
        } else {
            if ($operator == '>=' && $conditionValue == 0) {
                $currentTime = $this->_dateModel->gmtDate();
                $daysDiffSql = Mage::getResourceHelper('Enterprise_Reminder')
                    ->getDateDiff('quote.updated_at', $select->getAdapter()->formatDate($currentTime));
            }
            $select->where($daysDiffSql . " {$operator} ?", $conditionValue);
        }

        $select->where('quote.is_active = 1');
        $select->where('quote.items_count > 0');
        $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));
        Mage::getResourceHelper('Enterprise_Reminder')->setRuleLimit($select, 1);
        return $select;
    }

    /**
     * Get base SQL select
     *
     * @param   int|Zend_Db_Expr $customer
     * @param   int|Zend_Db_Expr $website
     * @return  Varien_Db_Select
     */
    public function getConditionsSql($customer, $website)
    {
        $select     = $this->_prepareConditionsSql($customer, $website);
        $required   = $this->_getRequiredValidation();
        $aggregator = ($this->getAggregator() == 'all') ? ' AND ' : ' OR ';
        $operator   = $required ? '=' : '<>';
        $conditions = array();

        foreach ($this->getConditions() as $condition) {
            if ($sql = $condition->getConditionsSql($customer, $website)) {
                $conditions[] = "(" . $select->getAdapter()->getIfNullSql("(" . $sql . ")", 0) . " {$operator} 1)";
            }
        }

        if (!empty($conditions)) {
            $select->where(implode($aggregator, $conditions));
        }

        return $select;
    }
}

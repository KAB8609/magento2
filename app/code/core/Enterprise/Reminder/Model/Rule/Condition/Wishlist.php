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
 * Customer wishlist conditions combine
 */
class Enterprise_Reminder_Model_Rule_Condition_Wishlist
    extends Enterprise_Reminder_Model_Condition_Combine_Abstract
{
    /**
     * class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('Enterprise_Reminder_Model_Rule_Condition_Wishlist');
        $this->setValue(null);
    }

    /**
     * Get list of available subconditions
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return Mage::getModel('Enterprise_Reminder_Model_Rule_Condition_Wishlist_Combine')->getNewChildSelectOptions();
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
     * @return Enterprise_Reminder_Model_Rule_Condition_Wishlist
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array());
        return $this;
    }

    /**
     * Prepare operator select options
     *
     * @return Enterprise_Reminder_Model_Rule_Condition_Wishlist
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
            . Mage::helper('enterprise_reminder')->__('Wishlist is not empty and abandoned %s %s days and %s of these conditions match:', $this->getOperatorElementHtml(), $this->getValueElementHtml(), $this->getAggregatorElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

     /**
     * Get condition SQL select
     *
     * @param $customer
     * @param $website
     * @return Varien_Db_Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $conditionValue = (int)$this->getValue();
        if ($conditionValue < 1) {
            Mage::throwException(Mage::helper('Enterprise_Reminder_Helper_Data')->__('Root wishlist condition should have days value at least 1.'));
        }

        $wishlistTable = $this->getResource()->getTable('wishlist');
        $wishlistItemTable = $this->getResource()->getTable('wishlist_item');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(array('item' => $wishlistItemTable), array(new Zend_Db_Expr(1)));

        $select->joinInner(
            array('list' => $wishlistTable),
            'item.wishlist_id = list.wishlist_id',
            array()
        );

        $this->_limitByStoreWebsite($select, $website, 'item.store_id');

        $currentTime = Mage::getModel('Mage_Core_Model_Date')->gmtDate();
        $daysDiffSql = Mage::getResourceHelper('Enterprise_Reminder')
            ->getDateDiff('list.updated_at', $select->getAdapter()->formatDate($currentTime));
        $select->where($daysDiffSql . " {$operator} ?", $conditionValue);
        $select->where($this->_createCustomerFilter($customer, 'list.customer_id'));
        Mage::getResourceHelper('Enterprise_Reminder')->setRuleLimit($select, 1);
        return $select;
    }

    /**
     * Get base SQL select
     *
     * @param $customer
     * @param $website
     * @return Varien_Db_Select
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
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
 * Cart items subselection condition
 */
class Enterprise_Reminder_Model_Rule_Condition_Cart_Subselection
    extends Enterprise_Reminder_Model_Condition_Combine_Abstract
{
    /**
     * @param Mage_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Mage_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Enterprise_Reminder_Model_Rule_Condition_Cart_Subselection');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return Mage::getModel('Enterprise_Reminder_Model_Rule_Condition_Cart_Subcombine')->getNewChildSelectOptions();
    }

    /**
     * Get element type for value select
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Prepare operator select options
     *
     * @return Enterprise_Reminder_Model_Rule_Condition_Cart_Subselection
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setOperatorOption(array(
            '==' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('found'),
            '!=' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('not found')
        ));
        return $this;
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('Enterprise_Reminder_Helper_Data')->__('If an item is %s in the shopping cart with %s of these conditions match:', $this->getOperatorElementHtml(), $this->getAggregatorElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Build query for matching shopping cart items
     *
     * @param $customer
     * @param int | Zend_Db_Expr $website
     * @return Varien_Db_Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();
        $quoteTable = $this->getResource()->getTable('sales_flat_quote');
        $quoteItemTable = $this->getResource()->getTable('sales_flat_quote_item');

        $select->from(array('item' => $quoteItemTable), array(new Zend_Db_Expr(1)));

        $select->joinInner(
            array('quote' => $quoteTable),
            'item.quote_id = quote.entity_id',
            array()
        );

        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');
        $select->where('quote.is_active = 1');
        $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));
        Mage::getResourceHelper('Enterprise_Reminder')->setRuleLimit($select, 1);

        return $select;
    }

    /**
     * Check if validation should be strict
     *
     * @return bool
     */
    protected function _getRequiredValidation()
    {
        return ($this->getOperator() == '==');
    }
}

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
 * Virtual cart condition
 */
namespace Magento\Reminder\Model\Rule\Condition\Cart;

class Virtual extends \Magento\Reminder\Model\Condition\AbstractCondition
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Rule\Model\Condition\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('\Magento\Reminder\Model\Rule\Condition\Cart\Virtual');
        $this->setValue(1);
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array('value' => $this->getType(),
            'label' => __('Virtual Only'));
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('Shopping cart %1 only virtual items', $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
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
     * Init list of available values
     *
     * @return \Magento\Reminder\Model\Rule\Condition\Cart\Virtual
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array(
            '1' => __('has'),
            '0' => __('does not have')
        ));
        return $this;
    }

    /**
     * Get SQL select
     *
     * @param $customer
     * @param int | \Zend_Db_Expr $website
     * @return \Magento\DB\Select
     */
    public function getConditionsSql($customer, $website)
    {
        $table = $this->getResource()->getTable('sales_flat_quote');

        $select = $this->getResource()->createSelect();
        $select->from(array('quote' => $table), array(new \Zend_Db_Expr(1)));

        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');
        $select->where('quote.is_active = 1');
        $select->where("quote.is_virtual = ?", $this->getValue());
        $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));
        $select->limit(1);
        return $select;
    }
}

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
 * Cart coupon code condition
 */
class Enterprise_Reminder_Model_Rule_Condition_Cart_Couponcode
    extends Enterprise_Reminder_Model_Condition_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('Enterprise_Reminder_Model_Rule_Condition_Cart_Couponcode');
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
            'label' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Coupon Code'));
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('enterprise_reminder')->__('Shopping cart %s a coupon applied', $this->getValueElementHtml())
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
     * @return Enterprise_Reminder_Model_Rule_Condition_Cart_Couponcode
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array(
            '1' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('has'),
            '0' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('does not have')
        ));
        return $this;
    }

    /**
     * Get SQL select
     *
     * @param $customer
     * @param int | Zend_Db_Expr $website
     * @return Varien_Db_Select
     */
    public function getConditionsSql($customer, $website)
    {
        $table = $this->getResource()->getTable('sales_flat_quote');
        $inversion = ((int)$this->getValue() ? '' : 'NOT');

        $select = $this->getResource()->createSelect();
        $select->from(array('quote' => $table), array(new Zend_Db_Expr(1)));

        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');
        $select->where('quote.is_active = 1');
        $select->where("{$inversion} ("
            . "quote.coupon_code IS NOT NULL AND quote.coupon_code <> " . $select->getAdapter()->quote('') . ")");
        $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));

        Mage::getResourceHelper('Enterprise_Reminder')->setRuleLimit($select, 1);

        return $select;
    }
}
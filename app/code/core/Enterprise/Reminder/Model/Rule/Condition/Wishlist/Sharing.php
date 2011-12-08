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
 * Wishlist sharing condition
 */
class Enterprise_Reminder_Model_Rule_Condition_Wishlist_Sharing
    extends Enterprise_Reminder_Model_Condition_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('Enterprise_Reminder_Model_Rule_Condition_Wishlist_Sharing');
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
            'label' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Sharing'));
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('Enterprise_Reminder_Helper_Data')->__('Wishlist %s shared',
                $this->getValueElementHtml())
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
     * @return Enterprise_Reminder_Model_Rule_Condition_Wishlist_Sharing
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array(
            '1' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('is'),
            '0' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('is not'),
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
        $table = $this->getResource()->getTable('wishlist');

        $select = $this->getResource()->createSelect();
        $select->from(array('list' => $table), array(new Zend_Db_Expr(1)));
        $select->where("list.shared = ?", $this->getValue());
        $select->where($this->_createCustomerFilter($customer, 'list.customer_id'));
        Mage::getResourceHelper('Enterprise_Reminder')->setRuleLimit($select, 1);

        return $select;
    }
}

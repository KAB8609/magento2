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
 * Wishlist subselection condition
 */
class Enterprise_Reminder_Model_Rule_Condition_Wishlist_Subselection
    extends Enterprise_Reminder_Model_Condition_Combine_Abstract
{
    /**
     * @param Mage_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Mage_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Enterprise_Reminder_Model_Rule_Condition_Wishlist_Subselection');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return Mage::getModel('Enterprise_Reminder_Model_Rule_Condition_Wishlist_Subcombine')
            ->getNewChildSelectOptions();
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
     * @return Enterprise_Reminder_Model_Rule_Condition_Wishlist_Subselection
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
            . Mage::helper('Enterprise_Reminder_Helper_Data')->__('If an item is %s in the wish list with %s of these conditions match:', $this->getOperatorElementHtml(), $this->getAggregatorElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Build query for matching wishlist items
     *
     * @param $customer
     * @param int | Zend_Db_Expr $website
     * @return Magento_DB_Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $wishlistTable = $this->getResource()->getTable('wishlist');
        $wishlistItemTable = $this->getResource()->getTable('wishlist_item');

        $select = $this->getResource()->createSelect();
        $select->from(array('item' => $wishlistItemTable), array(new Zend_Db_Expr(1)));

        $select->joinInner(
            array('list' => $wishlistTable),
            'item.wishlist_id = list.wishlist_id',
            array()
        );

        $this->_limitByStoreWebsite($select, $website, 'item.store_id');
        $select->where($this->_createCustomerFilter($customer, 'list.customer_id'));
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

    /**
     * Get field names map for subfilter conditions
     *
     * @return array
     */
    protected function _getSubfilterMap()
    {
        return array(
            'product' => 'item.product_id'
        );
    }
}

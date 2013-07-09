<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart/wishlist items condition
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Product_Combine_List
    extends Enterprise_CustomerSegment_Model_Condition_Combine_Abstract
{
    /**
     * Flag of using condition combine (for conditions of Product_Attribute)
     *
     * @var bool
     */
    protected $_combineProductCondition = true;

    const WISHLIST  = 'wishlist';
    const CART      = 'shopping_cart';

    /**
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * @param Mage_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Mage_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Enterprise_CustomerSegment_Model_Segment_Condition_Product_Combine_List');
        $this->setValue(self::CART);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        $events = array();
        switch ($this->getValue()) {
            case self::WISHLIST:
                $events = array('wishlist_items_renewed');
                break;
            default:
                $events = array('checkout_cart_save_after');
        }
        return $events;
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return Mage::getModel('Enterprise_CustomerSegment_Model_Segment_Condition_Product_Combine')
            ->setDateConditions(true)
            ->getNewChildSelectOptions();
    }

    /**
     * Initialize value select options
     *
     * @return Enterprise_CustomerSegment_Model_Segment_Condition_Product_Combine_List
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array(
            self::CART      => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Shopping Cart'),
            self::WISHLIST  => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Wish List'),
        ));
        return $this;
    }

    /**
     * Set rule instance
     *
     * Modify value_option array if needed
     *
     * @param Mage_Rule_Model_Rule $rule
     * @return Enterprise_CustomerSegment_Model_Segment_Condition_Product_Combine_List
     */
    public function setRule($rule)
    {
        $this->setData('rule', $rule);
        if ($rule instanceof Enterprise_CustomerSegment_Model_Segment && $rule->getApplyTo() !== null) {
            $option = $this->loadValueOptions()->getValueOption();
            $applyTo = $rule->getApplyTo();
            if (Enterprise_CustomerSegment_Model_Segment::APPLY_TO_VISITORS == $applyTo) {
                unset($option[self::WISHLIST]);
            } elseif (Enterprise_CustomerSegment_Model_Segment::APPLY_TO_VISITORS_AND_REGISTERED == $applyTo) {
                $option[self::CART] .= '*';
            }
            $this->setValueOption($option);
        }
        return $this;
    }

    /**
     * Get input type for attribute value.
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
     * @return Enterprise_CustomerSegment_Model_Segment_Condition_Product_Combine_List
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setOperatorOption(array(
            '=='  => Mage::helper('Mage_Rule_Helper_Data')->__('found'),
            '!='  => Mage::helper('Mage_Rule_Helper_Data')->__('not found')
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
            . Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('If Product is %s in the %s with %s of these Conditions match:', $this->getOperatorElementHtml(), $this->getValueElementHtml(), $this->getAggregatorElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Build query for matching shopping cart/wishlist items
     *
     * @param $customer
     * @param int | Zend_Db_Expr $website
     * @return Varien_Db_Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();

        switch ($this->getValue()) {
            case self::WISHLIST:
                $select->from(
                    array('item' => $this->getResource()->getTable('wishlist_item')),
                    array(new Zend_Db_Expr(1))
                );
                $conditions = "item.wishlist_id = list.wishlist_id";
                $select->joinInner(
                    array('list' => $this->getResource()->getTable('wishlist')),
                    $conditions,
                    array()
                );
                $this->_limitByStoreWebsite($select, $website, 'item.store_id');
                $select->where($this->_createCustomerFilter($customer, 'list.customer_id'));
                break;
            default:
                $select->from(
                    array('item' => $this->getResource()->getTable('sales_flat_quote_item')),
                    array(new Zend_Db_Expr(1))
                );
                $conditions = "item.quote_id = list.entity_id";
                $select->joinInner(
                    array('list' => $this->getResource()->getTable('sales_flat_quote')),
                    $conditions,
                    array()
                );
                $this->_limitByStoreWebsite($select, $website, 'list.store_id');
                $select->where('list.is_active = ?', new Zend_Db_Expr(1));
                if ($customer) {
                    // Leave ability to check this condition not only by customer_id but also by quote_id
                    $select->where('list.customer_id = :customer_id OR list.entity_id = :quote_id');
                } else {
                    $select->where($this->_createCustomerFilter($customer, 'list.customer_id'));
                }
                break;
        }

        Mage::getResourceHelper('Enterprise_CustomerSegment')->setOneRowLimit($select);

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
        switch ($this->getValue()) {
            case self::WISHLIST:
                $dateField = 'item.added_at';
                break;

            default:
                $dateField = 'item.created_at';
                break;
        }

        return array(
            'product' => 'item.product_id',
            'date'    => $dateField
        );
    }
}

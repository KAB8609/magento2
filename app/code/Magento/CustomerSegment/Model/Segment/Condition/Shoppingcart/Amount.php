<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart totals amount condition
 */
class Magento_CustomerSegment_Model_Segment_Condition_Shoppingcart_Amount
    extends Magento_CustomerSegment_Model_Condition_Abstract
{
    /**
     * @var string
     */
    protected $_inputType = 'numeric';

    /**
     * @param Magento_CustomerSegment_Model_Resource_Segment $resourceSegment
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_CustomerSegment_Model_Resource_Segment $resourceSegment,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        parent::__construct($resourceSegment, $context, $data);
        $this->setType('Magento_CustomerSegment_Model_Segment_Condition_Shoppingcart_Amount');
        $this->setValue(null);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        return array('sales_quote_save_commit_after');
    }

    /**
     * Get information for being presented in condition list
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array('value' => $this->getType(),
            'label' => __('Shopping Cart Total'),
            'available_in_guest_mode' => true);
    }

    /**
     * Init available options list
     *
     * @return Magento_CustomerSegment_Model_Segment_Condition_Shoppingcart_Amount
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'subtotal'  => __('Subtotal'),
            'grand_total'  => __('Grand Total'),
            'tax'  => __('Tax'),
            'shipping'  => __('Shipping'),
            'store_credit'  => __('Store Credit'),
            'gift_card'  => __('Gift Card'),
        ));
        return $this;
    }

    /**
     * Set rule instance
     *
     * Modify attribute_option array if needed
     *
     * @param Magento_Rule_Model_Rule $rule
     * @return Magento_CustomerSegment_Model_Segment_Condition_Product_Combine_List
     */
    public function setRule($rule)
    {
        $this->setData('rule', $rule);
        if ($rule instanceof Magento_CustomerSegment_Model_Segment && $rule->getApplyTo() !== null) {
            $attributeOption = $this->loadAttributeOptions()->getAttributeOption();
            $applyTo = $rule->getApplyTo();
            if (Magento_CustomerSegment_Model_Segment::APPLY_TO_VISITORS == $applyTo) {
                unset($attributeOption['store_credit']);
            } elseif (Magento_CustomerSegment_Model_Segment::APPLY_TO_VISITORS_AND_REGISTERED == $applyTo) {
                foreach (array_keys($attributeOption) as $key) {
                    if ('store_credit' != $key) {
                        $attributeOption[$key] .= '*';
                    }
                }
            }
            $this->setAttributeOption($attributeOption);
        }
        return $this;
    }

    /**
     * Condition string on conditions page
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml() . __('Shopping Cart %1 Amount %2 %3:',
            $this->getAttributeElementHtml(), $this->getOperatorElementHtml(), $this->getValueElementHtml()
        ) . $this->getRemoveLinkHtml();
    }

    /**
     * Build condition limitations sql string for specific website
     *
     * @param $customer
     * @param int | Zend_Db_Expr $website
     * @throws Magento_Core_Exception
     * @return Magento_DB_Select
     */
    public function getConditionsSql($customer, $website)
    {
        $table = $this->getResource()->getTable('sales_flat_quote');
        $addressTable = $this->getResource()->getTable('sales_flat_quote_address');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(array('quote'=>$table), array(new Zend_Db_Expr(1)))->where('quote.is_active=1');
        $select->limit(1);
        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');

        $joinAddress = false;
        switch ($this->getAttribute()) {
            case 'subtotal':
                $field = 'quote.base_subtotal';
                break;
            case 'grand_total':
                $field = 'quote.base_grand_total';
                break;
            case 'tax':
                $field = 'base_tax_amount';
                $joinAddress = true;
                break;
            case 'shipping':
                $field = 'base_shipping_amount';
                $joinAddress = true;
                break;
            case 'store_credit':
                $field = 'quote.base_customer_bal_amount_used';
                break;
            case 'gift_card':
                $field = 'quote.base_gift_cards_amount_used';
                break;
            default:
                throw new Magento_Core_Exception(__('Unknown quote total specified.'));
        }

        if ($joinAddress) {
            $subSelect = $this->getResource()->createSelect();
            $subSelect->from(
                array('address'=>$addressTable),
                array(
                    'quote_id' => 'quote_id',
                    $field     => new Zend_Db_Expr("SUM({$field})")
                )
            );

            $subSelect->group('quote_id');
            $select->joinInner(array('address' => $subSelect), 'address.quote_id = quote.entity_id', array());
            $field = "address.{$field}";
        }

        $select->where("{$field} {$operator} ?", $this->getValue());
        if ($customer) {
            // Leave ability to check this condition not only by customer_id but also by quote_id
            $select->where('quote.customer_id = :customer_id OR quote.entity_id = :quote_id');
        } else {
            $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));
        }
        return $select;
    }
}

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
 * Shopping cart product qty condition
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Shoppingcart_Productsquantity
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    protected $_inputType = 'numeric';

    public function __construct()
    {
        parent::__construct();
        $this->setType('Enterprise_CustomerSegment_Model_Segment_Condition_Shoppingcart_Productsquantity');
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
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array('value' => $this->getType(),
            'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Products Quantity'));
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Shopping Cart Products Qty %s %s:',
                $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Get SQL select for matching shopping cart products count
     *
     * @param $customer
     * @param int | Zend_Db_Expr $website
     * @return Varien_Db_Select
     */
    public function getConditionsSql($customer, $website)
    {
        $table = $this->getResource()->getTable('sales_flat_quote');
        $operator = $this->getResource()->getSqlOperator($this->getOperator());

        $select = $this->getResource()->createSelect();
        $select->from(array('quote'=>$table), array(new Zend_Db_Expr(1)));

        $this->_limitByStoreWebsite($select, $website, 'quote.store_id');
        Mage::getResourceHelper('Enterprise_CustomerSegment')->setOneRowLimit($select);

        $select->where("quote.items_qty {$operator} ?", $this->getValue());
        $select->where($this->_createCustomerFilter($customer, 'quote.customer_id'));

        return $select;
    }
}

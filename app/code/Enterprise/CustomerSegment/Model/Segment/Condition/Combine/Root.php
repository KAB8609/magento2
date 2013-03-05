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
 * Root segment condition (top level condition)
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Combine_Root
    extends Enterprise_CustomerSegment_Model_Segment_Condition_Combine
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('Enterprise_CustomerSegment_Model_Segment_Condition_Combine_Root');
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        return array('customer_login');
    }

    /**
     * Prepare filter condition by customer
     *
     * @param int|array|Mage_Customer_Model_Customer|Zend_Db_Select $customer
     * @param string $fieldName
     * @return string
     */
    protected function _createCustomerFilter($customer, $fieldName)
    {
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $customer = $customer->getId();
        } else if ($customer instanceof Zend_Db_Select) {
            $customer = new Zend_Db_Expr($customer);
        }

        return $this->getResource()->quoteInto("{$fieldName} IN (?)", $customer);
    }

    /**
     * Prepare base select with limitation by customer
     *
     * @param   null | array | int | Mage_Customer_Model_Customer $customer
     * @param   int | Zend_Db_Expr $website
     * @return  Varien_Db_Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $select = $this->getResource()->createSelect();
        $table = array('root' => $this->getResource()->getTable('customer_entity'));

        if ($customer) {
            // For existing customer
            $select->from($table, new Zend_Db_Expr(1));
        } else {
            $select->from($table, array('entity_id'));
            if ($customer === null) {
                if (Mage::getSingleton('Mage_Customer_Model_Config_Share')->isWebsiteScope()) {
                    $select->where('website_id=?', $website);
                }
            }
        }

        return $select;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Flat sales order collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Order_Collection extends Magento_Sales_Model_Resource_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_collection';

    /**
     * @var Magento_Core_Model_Resource_Helper_Mysql4
     */
    protected $_coreResourceHelper;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_Resource_Helper_Mysql4 $coreResourceHelper
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_Resource_Helper_Mysql4 $coreResourceHelper,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        parent::__construct($eventManager, $fetchStrategy, $resource);
        $this->_coreResourceHelper = $coreResourceHelper;
    }

    /**
     * Model initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Order', 'Magento_Sales_Model_Resource_Order');
        $this->addFilterToMap('entity_id', 'main_table.entity_id')
            ->addFilterToMap('customer_id', 'main_table.customer_id')
            ->addFilterToMap('quote_address_id', 'main_table.quote_address_id');
    }

    /**
     * Add items count expr to collection select, backward capability with eav structure
     *
     * @return Magento_Sales_Model_Resource_Order_Collection
     */
    public function addItemCountExpr()
    {
        if (is_null($this->_fieldsToSelect)) {
            // If we select all fields from table, we need to add column alias
            $this->getSelect()->columns(array('items_count'=>'total_item_count'));
        } else {
            $this->addFieldToSelect('total_item_count', 'items_count');
        }
        return $this;
    }

    /**
     * Minimize usual count select
     *
     * @return Magento_DB_Select
     */
    public function getSelectCountSql()
    {
        /* @var $countSelect Magento_DB_Select */
        $countSelect = parent::getSelectCountSql();
        $countSelect->resetJoinLeft();
        return $countSelect;
    }

    /**
     * Reset left join
     *
     * @param int $limit
     * @param int $offset
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getAllIdsSelect($limit = null, $offset = null)
    {
        $idsSelect = parent::_getAllIdsSelect($limit, $offset);
        $idsSelect->resetJoinLeft();
        return $idsSelect;
    }

    /**
     * Join table sales_flat_order_address to select for billing and shipping order addresses.
     * Create corillation map
     *
     * @return Magento_Sales_Model_Resource_Order_Collection
     */
    protected function _addAddressFields()
    {
        $billingAliasName = 'billing_o_a';
        $shippingAliasName = 'shipping_o_a';
        $joinTable = $this->getTable('sales_flat_order_address');

        $this
            ->addFilterToMap('billing_firstname', $billingAliasName . '.firstname')
            ->addFilterToMap('billing_lastname', $billingAliasName . '.lastname')
            ->addFilterToMap('billing_telephone', $billingAliasName . '.telephone')
            ->addFilterToMap('billing_postcode', $billingAliasName . '.postcode')

            ->addFilterToMap('shipping_firstname', $shippingAliasName . '.firstname')
            ->addFilterToMap('shipping_lastname', $shippingAliasName . '.lastname')
            ->addFilterToMap('shipping_telephone', $shippingAliasName . '.telephone')
            ->addFilterToMap('shipping_postcode', $shippingAliasName . '.postcode');

        $this
            ->getSelect()
            ->joinLeft(
                array($billingAliasName => $joinTable),
                "(main_table.entity_id = {$billingAliasName}.parent_id"
                    . " AND {$billingAliasName}.address_type = 'billing')",
                array(
                    $billingAliasName . '.firstname',
                    $billingAliasName . '.lastname',
                    $billingAliasName . '.telephone',
                    $billingAliasName . '.postcode'
                )
            )
            ->joinLeft(
                array($shippingAliasName => $joinTable),
                "(main_table.entity_id = {$shippingAliasName}.parent_id"
                    . " AND {$shippingAliasName}.address_type = 'shipping')",
                array(
                    $shippingAliasName . '.firstname',
                    $shippingAliasName . '.lastname',
                    $shippingAliasName . '.telephone',
                    $shippingAliasName . '.postcode'
                )
            );
        $this->_coreResourceHelper->prepareColumnsList($this->getSelect());
        return $this;
    }

    /**
     * Add addresses information to select
     *
     * @return Magento_Sales_Model_Resource_Collection_Abstract
     */
    public function addAddressFields()
    {
        return $this->_addAddressFields();
    }

    /**
     * Add field search filter to collection as OR condition
     *
     * @see self::_getConditionSql for $condition
     *
     * @param string $field
     * @param null|string|array $condition
     * @return Magento_Sales_Model_Resource_Order_Collection
     */
    public function addFieldToSearchFilter($field, $condition = null)
    {
        $field = $this->_getMappedField($field);
        $this->_select->orWhere($this->_getConditionSql($field, $condition));
        return $this;
    }

    /**
     * Specify collection select filter by attribute value
     *
     * @param array $attributes
     * @param array|integer|string|null $condition
     * @return Magento_Sales_Model_Resource_Order_Collection
     */
    public function addAttributeToSearchFilter($attributes, $condition = null)
    {
        if (is_array($attributes) && !empty($attributes)) {
            $this->_addAddressFields();

            foreach ($attributes as $attribute) {
                $this->addFieldToSearchFilter($this->_attributeToField($attribute['attribute']), $attribute);
            }
        } else {
            $this->addAttributeToFilter($attributes, $condition);
        }

        return $this;
    }

    /**
     * Add filter by specified billing agreements
     *
     * @param int|array $agreements
     * @return Magento_Sales_Model_Resource_Order_Collection
     */
    public function addBillingAgreementsFilter($agreements)
    {
        $agreements = (is_array($agreements)) ? $agreements : array($agreements);
        $this->getSelect()
            ->joinInner(
                array('sbao' => $this->getTable('sales_billing_agreement_order')),
                'main_table.entity_id = sbao.order_id',
                array())
            ->where('sbao.agreement_id IN(?)', $agreements);
        return $this;
    }

    /**
     * Add filter by specified recurring profile id(s)
     *
     * @param array|int $ids
     * @return Magento_Sales_Model_Resource_Order_Collection
     */
    public function addRecurringProfilesFilter($ids)
    {
        $ids = (is_array($ids)) ? $ids : array($ids);
        $this->getSelect()
            ->joinInner(
                array('srpo' => $this->getTable('sales_recurring_profile_order')),
                'main_table.entity_id = srpo.order_id',
                array())
            ->where('srpo.profile_id IN(?)', $ids);
        return $this;
    }
}

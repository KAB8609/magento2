<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog product abstract price backend attribute model with customer group specific
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice_Abstract
    extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Load Tier Prices for product
     *
     * @param int $productId
     * @param int $websiteId
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice
     */
    public function loadPriceData($productId, $websiteId = null)
    {
        $adapter = $this->_getReadAdapter();

        $columns = array(
            'price_id'      => $this->getIdFieldName(),
            'website_id'    => 'website_id',
            'all_groups'    => 'all_groups',
            'cust_group'    => 'customer_group_id',
            'price'         => 'value',
        );

        $columns = $this->_loadPriceDataColumns($columns);

        $select  = $adapter->select()
            ->from($this->getMainTable(), $columns)
            ->where('entity_id=?', $productId);

        $this->_loadPriceDataSelect($select);

        if (!is_null($websiteId)) {
            if ($websiteId == '0') {
                $select->where('website_id = ?', $websiteId);
            } else {
                $select->where('website_id IN(?)', array(0, $websiteId));
            }
        }

        return $adapter->fetchAll($select);
    }

    /**
     * Load specific sql columns
     *
     * @param array $columns
     * @return array
     */
    protected function _loadPriceDataColumns($columns)
    {
        return $columns;
    }

    /**
     * Load specific db-select data
     *
     * @param Magento_DB_Select $select
     * @return Magento_DB_Select
     */
    protected function _loadPriceDataSelect($select)
    {
        return $select;
    }

    /**
     * Delete Tier Prices for product
     *
     * @param int $productId
     * @param int $websiteId
     * @param int $priceId
     * @return int The number of affected rows
     */
    public function deletePriceData($productId, $websiteId = null, $priceId = null)
    {
        $adapter = $this->_getWriteAdapter();

        $conds   = array(
            $adapter->quoteInto('entity_id = ?', $productId)
        );

        if (!is_null($websiteId)) {
            $conds[] = $adapter->quoteInto('website_id = ?', $websiteId);
        }

        if (!is_null($priceId)) {
            $conds[] = $adapter->quoteInto($this->getIdFieldName() . ' = ?', $priceId);
        }

        $where = implode(' AND ', $conds);

        return $adapter->delete($this->getMainTable(), $where);
    }

    /**
     * Save tier price object
     *
     * @param Magento_Object $priceObject
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice
     */
    public function savePriceData(Magento_Object $priceObject)
    {
        $adapter = $this->_getWriteAdapter();
        $data    = $this->_prepareDataForTable($priceObject, $this->getMainTable());

        if (!empty($data[$this->getIdFieldName()])) {
            $where = $adapter->quoteInto($this->getIdFieldName() . ' = ?', $data[$this->getIdFieldName()]);
            unset($data[$this->getIdFieldName()]);
            $adapter->update($this->getMainTable(), $data, $where);
        } else {
            $adapter->insert($this->getMainTable(), $data);
        }
        return $this;
    }
}
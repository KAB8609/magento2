<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift refistry type resource collection
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftRegistry_Model_Resource_Type_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * If the table was joined flag
     *
     * @var bool
     */
    protected $_isTableJoined                       = false;

    /**
     * Collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Enterprise_GiftRegistry_Model_Type', 'Enterprise_GiftRegistry_Model_Resource_Type');
    }

    /**
     * Add store data to collection
     *
     * @param int $storeId
     * @return Enterprise_GiftRegistry_Model_Resource_Type_Collection
     */
    public function addStoreData($storeId = Magento_Core_Model_AppInterface::ADMIN_STORE_ID)
    {
        $infoTable = $this->getTable('enterprise_giftregistry_type_info');
        $adapter   = $this->getConnection();

        $select = $adapter->select();
        $select->from(array('m' => $this->getMainTable()))
            ->joinInner(
                array('d' => $infoTable),
                $adapter->quoteInto('m.type_id = d.type_id AND d.store_id = ?', Magento_Core_Model_AppInterface::ADMIN_STORE_ID),
                array())
            ->joinLeft(
                array('s' => $infoTable),
                $adapter->quoteInto('s.type_id = m.type_id AND s.store_id = ?', (int)$storeId),
                array(
                    'label'     => $adapter->getCheckSql('s.label IS NULL', 'd.label', 's.label'),
                    'is_listed' => $adapter->getCheckSql('s.is_listed IS NULL', 'd.is_listed', 's.is_listed'),
                    'sort_order'=> $adapter->getCheckSql('s.sort_order IS NULL', 'd.sort_order', 's.sort_order')
            ));

        $this->getSelect()->reset()->from(array('main_table' => $select));

        $this->_isTableJoined = true;

        return $this;
    }

    /**
     * Filter collection by listed param
     *
     * @return Enterprise_GiftRegistry_Model_Resource_Type_Collection
     */
    public function applyListedFilter()
    {
        if ($this->_isTableJoined) {
            $this->getSelect()->where('is_listed = ?', 1);
        }
        return $this;
    }

    /**
     * Apply sorting by sort_order param
     *
     * @return Enterprise_GiftRegistry_Model_Resource_Type_Collection
     */
    public function applySortOrder()
    {
        if ($this->_isTableJoined) {
            $this->getSelect()->order('sort_order');
        }
        return $this;
    }

    /**
     * Convert collection to array for select options
     *
     * @param bool $withEmpty
     * @return array
     */
    public function toOptionArray($withEmpty = false)
    {
        $result = $this->_toOptionArray('type_id', 'label');
        if ($withEmpty) {
            $result = array_merge(array(array(
                'value' => '',
                'label' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('-- All --')
            )), $result);
        }
        return $result;
    }
}

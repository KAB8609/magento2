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
 * Flat sales abstract collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Sales_Model_Resource_Collection_Abstract extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Check if $attribute is Magento_Eav_Model_Entity_Attribute and convert to string field name
     *
     * @param string|Magento_Eav_Model_Entity_Attribute $attribute
     * @return string
     */
    protected function _attributeToField($attribute)
    {
        $field = false;
        if (is_string($attribute)) {
            $field = $attribute;
        } elseif ($attribute instanceof Magento_Eav_Model_Entity_Attribute) {
            $field = $attribute->getAttributeCode();
        }
        if (!$field) {
            Mage::throwException(__('We cannot determine the field name.'));
        }
        return $field;
    }

    /**
     * Add attribute to select result set.
     * Backward compatibility with EAV collection
     *
     * @param string $attribute
     * @return Magento_Sales_Model_Resource_Collection_Abstract
     */
    public function addAttributeToSelect($attribute)
    {
        $this->addFieldToSelect($this->_attributeToField($attribute));
        return $this;
    }

    /**
     * Specify collection select filter by attribute value
     * Backward compatibility with EAV collection
     *
     * @param string|Magento_Eav_Model_Entity_Attribute $attribute
     * @param array|integer|string|null $condition
     * @return Magento_Sales_Model_Resource_Collection_Abstract
     */
    public function addAttributeToFilter($attribute, $condition = null)
    {
        $this->addFieldToFilter($this->_attributeToField($attribute), $condition);
        return $this;
    }

    /**
     * Specify collection select order by attribute value
     * Backward compatibility with EAV collection
     *
     * @param string $attribute
     * @param string $dir
     * @return Magento_Sales_Model_Resource_Collection_Abstract
     */
    public function addAttributeToSort($attribute, $dir = 'asc')
    {
        $this->addOrder($this->_attributeToField($attribute), $dir);
        return $this;
    }

    /**
     * Set collection page start and records to show
     * Backward compatibility with EAV collection
     *
     * @param integer $pageNum
     * @param integer $pageSize
     * @return Magento_Sales_Model_Resource_Collection_Abstract
     */
    public function setPage($pageNum, $pageSize)
    {
        $this->setCurPage($pageNum)
            ->setPageSize($pageSize);
        return $this;
    }

    /**
     * Create all ids retrieving select with limitation
     * Backward compatibility with EAV collection
     *
     * @param int $limit
     * @param int $offset
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getAllIdsSelect($limit = null, $offset = null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
        $idsSelect->limit($limit, $offset);
        return $idsSelect;
    }

    /**
     * Retrive all ids for collection
     * Backward compatibility with EAV collection
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol(
            $this->_getAllIdsSelect($limit, $offset),
            $this->_bindParams
        );
    }

    /**
     * Backward compatibility with EAV collection
     *
     * @todo implement join functionality if necessary
     *
     * @param string $alias
     * @param string $attribute
     * @param string $bind
     * @param string $filter
     * @param string $joinType
     * @param int $storeId
     * @return Magento_Sales_Model_Resource_Collection_Abstract
     */
    public function joinAttribute($alias, $attribute, $bind, $filter = null, $joinType = 'inner', $storeId = null)
    {
        return $this;
    }
}
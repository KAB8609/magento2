<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Eav attribute set resource model
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Resource_Entity_Attribute_Set extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('eav_attribute_set', 'attribute_set_id');
    }

    /**
     * Perform actions after object save
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Eav_Model_Resource_Entity_Attribute_Set
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        if ($object->getGroups()) {
            /* @var $group Magento_Eav_Model_Entity_Attribute_Group */
            foreach ($object->getGroups() as $group) {
                $group->setAttributeSetId($object->getId());
                if ($group->itemExists() && !$group->getId()) {
                    continue;
                }
                $group->save();
            }
        }
        if ($object->getRemoveGroups()) {
            foreach ($object->getRemoveGroups() as $group) {
                /* @var $group Magento_Eav_Model_Entity_Attribute_Group */
                $group->delete();
            }
            Mage::getResourceModel('Magento_Eav_Model_Resource_Entity_Attribute_Group')
                ->updateDefaultGroup($object->getId());
        }
        if ($object->getRemoveAttributes()) {
            foreach ($object->getRemoveAttributes() as $attribute) {
                /* @var $attribute Magento_Eav_Model_Entity_Attribute */
                $attribute->deleteEntity();
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Validate attribute set name
     *
     * @param Magento_Eav_Model_Entity_Attribute_Set $object
     * @param string $attributeSetName
     * @return bool
     */
    public function validate($object, $attributeSetName)
    {

        $adapter = $this->_getReadAdapter();
        $bind = array(
            'attribute_set_name' => trim($attributeSetName),
            'entity_type_id'     => $object->getEntityTypeId()
        );
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('attribute_set_name = :attribute_set_name')
            ->where('entity_type_id = :entity_type_id');

        if ($object->getId()) {
            $bind['attribute_set_id'] = $object->getId();
            $select->where('attribute_set_id != :attribute_set_id');
        }

        return !$adapter->fetchOne($select, $bind) ? true : false;
    }

    /**
     * Retrieve Set info by attributes
     *
     * @param array $attributeIds
     * @param int $setId
     * @return array
     */
    public function getSetInfo(array $attributeIds, $setId = null)
    {
        $adapter = $this->_getReadAdapter();
        $setInfo = array();
        $attributeToSetInfo = array();

        if (count($attributeIds) > 0) {
            $select = $adapter->select()
                ->from(
                    array('entity' => $this->getTable('eav_entity_attribute')),
                    array('attribute_id', 'attribute_set_id', 'attribute_group_id', 'sort_order'))
                ->joinLeft(
                    array('attribute_group' => $this->getTable('eav_attribute_group')),
                    'entity.attribute_group_id = attribute_group.attribute_group_id',
                    array('group_sort_order' => 'sort_order'))
                ->where('entity.attribute_id IN (?)', $attributeIds);
            $bind = array();
            if (is_numeric($setId)) {
                $bind[':attribute_set_id'] = $setId;
                $select->where('entity.attribute_set_id = :attribute_set_id');
            }
            $result = $adapter->fetchAll($select, $bind);

            foreach ($result as $row) {
                $data = array(
                    'group_id'      => $row['attribute_group_id'],
                    'group_sort'    => $row['group_sort_order'],
                    'sort'          => $row['sort_order']
                );
                $attributeToSetInfo[$row['attribute_id']][$row['attribute_set_id']] = $data;
            }
        }

        foreach ($attributeIds as $atttibuteId) {
            $setInfo[$atttibuteId] = isset($attributeToSetInfo[$atttibuteId])
                ? $attributeToSetInfo[$atttibuteId]
                : array();
        }

        return $setInfo;
    }

    /**
     * Retrurn default attribute group id for attribute set id
     *
     * @param int $setId
     * @return int|null
     */
    public function getDefaultGroupId($setId)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array(
            'attribute_set_id' => (int)$setId
        );
        $select = $adapter->select()
            ->from($this->getTable('eav_attribute_group'), 'attribute_group_id')
            ->where('attribute_set_id = :attribute_set_id')
            ->where('default_id = 1')
            ->limit(1);
        return $adapter->fetchOne($select, $bind);
    }
}
<?php

class Mage_Eav_Model_Mysql4_Entity_Attribute_Set extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_beforeSaveAttributes;
    
    protected function _construct()
    {
        $this->_init('eav/attribute_set', 'attribute_set_id');
    }
    
    public function save(Mage_Core_Model_Abstract $object)
    {
        $write = $this->getConnection('write');
        $setId = $object->getId();

        $data = array(
            'entity_type_id' => $object->getEntityTypeId(),
            'attribute_set_name' => $object->getAttributeSetName(),
        );

        $write->beginTransaction();
        try {
            if( intval($setId) > 0 ) {
                $condition = $write->quoteInto("{$this->getMainTable()}.{$this->getIdFieldName()} = ?", $setId);
                $write->update($this->getMainTable(), $data, $condition);

                if( $object->getGroups() ) {
                    foreach( $object->getGroups() as $group ) {
                        $group->save();
                    }
                }

                if( $object->getRemoveGroups() ) {
                    foreach( $object->getRemoveGroups() as $group ) {
                        $group->delete($group->getId());
                    }
                }

                if( $object->getRemoveAttributes() ) {
                    foreach( $object->getRemoveAttributes() as $attribute ) {
                        $attribute->deleteEntity();
                    }
                }

            } else {
                $write->insert($this->getMainTable(), $data);
                $object->setId($write->lastInsertId());
            }
            $write->commit();
        } catch (Exception $e) {
            $write->rollback();
            throw new Exception($e->getMessage());
        }
    }
}
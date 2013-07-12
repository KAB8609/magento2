<?php
/**
 * Oder statuses grid collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Sales_Model_Resource_Status_Collection extends Mage_Sales_Model_Resource_Order_Status_Collection
{
    /**
     * Join order states table
     *
     * @return Mage_Sales_Model_Resource_Status_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->joinStates();
        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 *Quote address attribute backend parent resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Quote_Address_Attribute_Backend_Parent
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Save items collection and shipping rates collection
     *
     * @param Varien_Object $object
     * @return Mage_Sales_Model_Resource_Quote_Address_Attribute_Backend_Parent
     */
    public function afterSave($object)
    {
        parent::afterSave($object);
        
        $object->getItemsCollection()->save();
        $object->getShippingRatesCollection()->save();
        
        return $this;
    }
}
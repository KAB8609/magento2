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
 *
 * Speical Start Date attribute backend
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Catalog_Model_Product_Attribute_Backend_Startdate extends Magento_Eav_Model_Entity_Attribute_Backend_Datetime
{
   /**
    * Get attribute value for save.
    *
    * @param Magento_Object $object
    * @return string|bool
    */
    protected function _getValueForSave($object)
    {
        $attributeName  = $this->getAttribute()->getName();
        $startDate      = $object->getData($attributeName);
        if ($startDate === false) {
            return false;
        }
        if ($startDate == '' && $object->getSpecialPrice()) {
            $startDate = Mage::app()->getLocale()->date();
        }

        return $startDate;
    }

   /**
    * Before save hook.
    * Prepare attribute value for save
    *
    * @param Magento_Object $object
    * @return Magento_Catalog_Model_Product_Attribute_Backend_Startdate
    */
    public function beforeSave($object)
    {
        $startDate = $this->_getValueForSave($object);
        if ($startDate === false) {
            return $this;
        }

        $object->setData($this->getAttribute()->getName(), $startDate);
        parent::beforeSave($object);
        return $this;
    }

   /**
    * Product from date attribute validate function.
    * In case invalid data throws exception.
    *
    * @param Magento_Object $object
    * @throws Magento_Eav_Model_Entity_Attribute_Exception
    * @return bool
    */
    public function validate($object)
    {
        $attr      = $this->getAttribute();
        $maxDate   = $attr->getMaxValue();
        $startDate = $this->_getValueForSave($object);
        if ($startDate === false) {
            return true;
        }

        if ($maxDate) {
            $date     = Mage::getModel('Magento_Core_Model_Date');
            $value    = $date->timestamp($startDate);
            $maxValue = $date->timestamp($maxDate);

            if ($value > $maxValue) {
                $message = Mage::helper('Magento_Catalog_Helper_Data')->__('The From Date value should be less than or equal to the To Date value.');
                $eavExc  = new Magento_Eav_Model_Entity_Attribute_Exception($message);
                $eavExc->setAttributeCode($attr->getName());
                throw $eavExc;
            }
        }
        return true;
    }
}

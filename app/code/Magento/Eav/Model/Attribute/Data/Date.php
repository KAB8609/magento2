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
 * EAV Entity Attribute Date Data Model
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Attribute_Data_Date extends Magento_Eav_Model_Attribute_Data_Abstract
{
    /**
     * Extract data from request and return value
     *
     * @param Zend_Controller_Request_Http $request
     * @return array|string
     */
    public function extractValue(Zend_Controller_Request_Http $request)
    {
        $value = $this->_getRequestValue($request);
        return $this->_applyInputFilter($value);
    }

    /**
     * Validate data
     * Return true or array of errors
     *
     * @param array|string $value
     * @return boolean|array
     */
    public function validateValue($value)
    {
        $errors     = array();
        $attribute  = $this->getAttribute();
        $label      = $attribute->getStoreLabel();

        if ($value === false) {
            // try to load original value and validate it
            $value = $this->getEntity()->getDataUsingMethod($attribute->getAttributeCode());
        }

        if ($attribute->getIsRequired() && empty($value)) {
            $errors[] = Mage::helper('Magento_Eav_Helper_Data')->__('"%s" is a required value.', $label);
        }

        if (!$errors && !$attribute->getIsRequired() && empty($value)) {
            return true;
        }

        $result = $this->_validateInputRule($value);
        if ($result !== true) {
            $errors = array_merge($errors, $result);
        }

        //range validation
        $validateRules = $attribute->getValidateRules();
        if ((!empty($validateRules['date_range_min']) && (strtotime($value) < $validateRules['date_range_min']))
            || (!empty($validateRules['date_range_max']) && (strtotime($value) > $validateRules['date_range_max']))
        ) {
            if (!empty($validateRules['date_range_min']) && !empty($validateRules['date_range_max'])) {
                $errors[] = Mage::helper('Mage_Customer_Helper_Data')->__('Please enter a valid date between %s and %s at %s.', date('d/m/Y', $validateRules['date_range_min']), date('d/m/Y', $validateRules['date_range_max']), $label);
            } elseif (!empty($validateRules['date_range_min'])) {
                $errors[] = Mage::helper('Mage_Customer_Helper_Data')->__('Please enter a valid date equal to or greater than %s at %s.', date('d/m/Y', $validateRules['date_range_min']), $label);
            } elseif (!empty($validateRules['date_range_max'])) {
                $errors[] = Mage::helper('Mage_Customer_Helper_Data')->__('Please enter a valid date less than or equal to %s at %s.', date('d/m/Y', $validateRules['date_range_max']), $label);
            }
        }

        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }

    /**
     * Export attribute value to entity model
     *
     * @param array|string $value
     * @return Magento_Eav_Model_Attribute_Data_Date
     */
    public function compactValue($value)
    {
        if ($value !== false) {
            if (empty($value)) {
                $value = null;
            }
            $this->getEntity()->setDataUsingMethod($this->getAttribute()->getAttributeCode(), $value);
        }
        return $this;
    }

    /**
     * Restore attribute value from SESSION to entity model
     *
     * @param array|string $value
     * @return Magento_Eav_Model_Attribute_Data_Date
     */
    public function restoreValue($value)
    {
        return $this->compactValue($value);
    }

    /**
     * Return formated attribute value from entity model
     *
     * @param string $format
     * @return string|array
     */
    public function outputValue($format = Magento_Eav_Model_Attribute_Data::OUTPUT_FORMAT_TEXT)
    {
        $value = $this->getEntity()->getData($this->getAttribute()->getAttributeCode());
        if ($value) {
            switch ($format) {
                case Magento_Eav_Model_Attribute_Data::OUTPUT_FORMAT_TEXT:
                case Magento_Eav_Model_Attribute_Data::OUTPUT_FORMAT_HTML:
                case Magento_Eav_Model_Attribute_Data::OUTPUT_FORMAT_PDF:
                    $this->_dateFilterFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM);
                    break;
            }
            $value = $this->_applyOutputFilter($value);
        }

        $this->_dateFilterFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);

        return $value;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV Entity Attribute Image File Data Model
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Attribute_Data_Image extends Mage_Eav_Model_Attribute_Data_File
{
    /**
     * Validate file by attribute validate rules
     * Return array of errors
     *
     * @param array $value
     * @return array
     */
    protected function _validateByRules($value)
    {
        $label  = Mage::helper('Mage_Eav_Helper_Data')->__($this->getAttribute()->getStoreLabel());
        $rules  = $this->getAttribute()->getValidateRules();

        $imageProp = @getimagesize($value['tmp_name']);

        if (!is_uploaded_file($value['tmp_name']) || !$imageProp) {
            return array(
                Mage::helper('Mage_Eav_Helper_Data')->__('"%1" is not a valid file', $label)
            );
        }

        $allowImageTypes = array(
            1   => 'gif',
            2   => 'jpg',
            3   => 'png',
        );

        if (!isset($allowImageTypes[$imageProp[2]])) {
            return array(
                Mage::helper('Mage_Eav_Helper_Data')->__('"%1" is not a valid image format', $label)
            );
        }

        // modify image name
        $extension  = pathinfo($value['name'], PATHINFO_EXTENSION);
        if ($extension != $allowImageTypes[$imageProp[2]]) {
            $value['name'] = pathinfo($value['name'], PATHINFO_FILENAME) . '.' . $allowImageTypes[$imageProp[2]];
        }

        $errors = array();
        if (!empty($rules['max_file_size'])) {
            $size = $value['size'];
            if ($rules['max_file_size'] < $size) {
                $errors[] = Mage::helper('Mage_Eav_Helper_Data')->__('"%1" exceeds the allowed file size.', $label);
            };
        }

        if (!empty($rules['max_image_width'])) {
            if ($rules['max_image_width'] < $imageProp[0]) {
                $r = $rules['max_image_width'];
                $errors[] = Mage::helper('Mage_Eav_Helper_Data')->__('"%1" width exceeds allowed value of %2 px.', $label, $r);
            };
        }
        if (!empty($rules['max_image_heght'])) {
            if ($rules['max_image_heght'] < $imageProp[1]) {
                $r = $rules['max_image_heght'];
                $errors[] = Mage::helper('Mage_Eav_Helper_Data')->__('"%1" height exceeds allowed value of %2 px.', $label, $r);
            };
        }

        return $errors;
    }
}

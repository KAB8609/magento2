<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Model_Config_Backend_Baseurl extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $value = $this->getValue();

        if (!preg_match('#^{{((un)?secure_)?(base|public)_url}}#', $value)) {
            $parsedUrl = parse_url($value);
            if (!isset($parsedUrl['scheme']) || !isset($parsedUrl['host'])) {
                $fieldConfig = $this->getFieldConfig();
                $exceptionMsg = Mage::helper('Mage_Core_Helper_Data')
                    ->__('The %s you entered is invalid. Please make sure that it follows "http://domain.com/" format.',
                    $fieldConfig['label']
                );
                Mage::throwException($exceptionMsg);
            }
        }

        $value = rtrim($value, '/');
        /**
         * If value is special ({{}}) we don't need add slash
         */
        if (!preg_match('#}}$#', $value)) {
            $value.= '/';
        }


        $this->setValue($value);
        return $this;
    }

    /**
     * Clean compiled JS/CSS when updating url configuration settings
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            Mage::getModel('Mage_Core_Model_Design_Package')->cleanMergedJsCss();
        }
    }
}
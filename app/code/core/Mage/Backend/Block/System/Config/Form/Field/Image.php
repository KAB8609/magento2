<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Image config field renderer
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_System_Config_Form_Field_Image extends Varien_Data_Form_Element_Image
{

    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        $url = parent::_getUrl();

        $config = $this->getFieldConfig();
        /* @var $config array */
        if (array_key_exists('base_url', $config)) {
            $element = $config['base_url'];
            $urlType = empty($element['type']) ? 'link' : (string)$element['type'];
            $url = Mage::getBaseUrl($urlType) . $element['value'] . '/' . $url;
        }

        return $url;
    }

}

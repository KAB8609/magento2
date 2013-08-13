<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Image form element that generates correct thumbnail image URL for theme preview image
 */
class Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_Image extends Magento_Data_Form_Element_Image
{
    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = Mage::getObjectManager()->get('Magento_Core_Model_Theme_Image_Path')->getPreviewImageDirectoryUrl()
                . $this->getValue();
        }
        return $url;
    }
}

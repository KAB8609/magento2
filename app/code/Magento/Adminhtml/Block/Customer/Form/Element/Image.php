<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Widget Form Image File Element Block
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_Form_Element_Image extends Magento_Adminhtml_Block_Customer_Form_Element_File
{
    /**
     * Return Delete CheckBox Label
     *
     * @return string
     */
    protected function _getDeleteCheckboxLabel()
    {
        return Mage::helper('Magento_Adminhtml_Helper_Data')->__('Delete Image');
    }

    /**
     * Return Delete CheckBox SPAN Class name
     *
     * @return string
     */
    protected function _getDeleteCheckboxSpanClass()
    {
        return 'delete-image';
    }

    /**
     * Return File preview link HTML
     *
     * @return string
     */
    protected function _getPreviewHtml()
    {
        $html = '';
        if ($this->getValue() && !is_array($this->getValue())) {
            $url = $this->_getPreviewUrl();
            $imageId = sprintf('%s_image', $this->getHtmlId());
            $image   = array(
                'alt'    => Mage::helper('Magento_Adminhtml_Helper_Data')->__('View Full Size'),
                'title'  => Mage::helper('Magento_Adminhtml_Helper_Data')->__('View Full Size'),
                'src'    => $url,
                'class'  => 'small-image-preview v-middle',
                'height' => 22,
                'width'  => 22,
                'id'     => $imageId
            );
            $link    = array(
                'href'      => $url,
                'onclick'   => "imagePreview('{$imageId}'); return false;",
            );

            $html = sprintf('%s%s</a> ',
                $this->_drawElementHtml('a', $link, false),
                $this->_drawElementHtml('img', $image)
            );
        }
        return $html;
    }

    /**
     * Return Image URL
     *
     * @return string
     */
    protected function _getPreviewUrl()
    {
        if (is_array($this->getValue())) {
            return false;
        }
        return Mage::helper('Magento_Adminhtml_Helper_Data')->getUrl('adminhtml/customer/viewfile', array(
            'image'      => Mage::helper('Magento_Core_Helper_Data')->urlEncode($this->getValue()),
        ));
    }
}

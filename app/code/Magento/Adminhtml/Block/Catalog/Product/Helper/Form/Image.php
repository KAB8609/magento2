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
 * Product form image field helper
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Helper\Form;

class Image extends \Magento\Data\Form\Element\Image
{
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = \Mage::getBaseUrl('media').'catalog/product/'. $this->getValue();
        }
        return $url;
    }
    
    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ($attribute = $this->getEntityAttribute()) {
            if (!$attribute->getIsRequired()) {
                $html.= parent::_getDeleteCheckbox();
            }
            else {
                $html.= '<input value="'.$this->getValue().'" id="'.$this->getHtmlId().'_hidden" type="hidden" class="required-entry" />';
                $html.= '<script type="text/javascript">
                    syncOnchangeValue(\''.$this->getHtmlId().'\', \''.$this->getHtmlId().'_hidden\');
                </script>';
            }
        }
        else {
            $html.= parent::_getDeleteCheckbox();
        }
        return $html;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog textarea attribute WYSIWYG button
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Helper_Form_Wysiwyg extends Varien_Data_Form_Element_Textarea
{
    /**
     * Retrieve additional html and put it at the end of element html
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        if ($this->getIsWysiwygEnabled()) {
            $disabled = ($this->getDisabled() || $this->getReadonly());
            $html .= Mage::app()->getLayout()
                ->createBlock('Mage_Adminhtml_Block_Widget_Button', '', array(
                    'label'   => Mage::helper('Mage_Catalog_Helper_Data')->__('WYSIWYG Editor'),
                    'type'    => 'button',
                    'disabled' => $disabled,
                    'class' => ($disabled) ? 'disabled btn-wysiwyg' : 'btn-wysiwyg',
                    'onclick' => 'catalogWysiwygEditor.open(\''
                        . Mage::helper('Mage_Adminhtml_Helper_Data')->getUrl('*/*/wysiwyg')
                        . '\', \'' . $this->getHtmlId().'\')'
                ))->toHtml();
            $html .= <<<HTML
<script type="text/javascript">
jQuery('#{$this->getHtmlId()}').data(
    'wysiwygEditor',
    new tinyMceWysiwygSetup(
        '{$this->getHtmlId()}',
         {
            settings: {
                theme_advanced_buttons1 : 'magentowidget,bold,italic,|,justifyleft,justifycenter,justifyright,|,fontselect,fontsizeselect,|,forecolor,backcolor,|,link,unlink,image,|,bullist,numlist',
                theme_advanced_buttons2: null,
                theme_advanced_buttons3: null,
                theme_advanced_buttons4: null,
                theme_advanced_statusbar_location: null
            }
         }
    ).turnOn()
)
</script>
HTML;

        }
        return $html;
    }

    /**
     * Check whether wysiwyg enabled or not
     *
     * @return boolean
     */
    public function getIsWysiwygEnabled()
    {
        if (Mage::helper('Mage_Catalog_Helper_Data')->isModuleEnabled('Mage_Cms')) {
            return (bool)(Mage::getSingleton('Mage_Cms_Model_Wysiwyg_Config')->isEnabled()
                && $this->getEntityAttribute()->getIsWysiwygEnabled());
        }

        return false;
    }
}

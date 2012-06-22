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
 * Wysiwyg controller for different purposes
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Cms_WysiwygController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Template directives callback
     *
     * TODO: move this to some model
     */
    public function directiveAction()
    {
        $directive = $this->getRequest()->getParam('___directive');
        $directive = Mage::helper('Mage_Core_Helper_Data')->urlDecode($directive);
        $url = Mage::getModel('Mage_Core_Model_Email_Template_Filter')->filter($directive);
        $adapter = Mage::helper('Mage_Core_Helper_Data')->getImageAdapterType();
        try {
            $image = Varien_Image_Adapter::factory($adapter);
            $image->open($url);
            $image->display();
        } catch (Exception $e) {
            $image = Varien_Image_Adapter::factory($adapter);
            $image->open(Mage::getSingleton('Mage_Cms_Model_Wysiwyg_Config')->getSkinImagePlaceholderUrl());
            $image->display();
            /*
            $image = imagecreate(100, 100);
            $bkgrColor = imagecolorallocate($image,10,10,10);
            imagefill($image,0,0,$bkgrColor);
            $textColor = imagecolorallocate($image,255,255,255);
            imagestring($image, 4, 10, 10, 'Skin image', $textColor);
            header('Content-type: image/png');
            imagepng($image);
            imagedestroy($image);
            */
        }
    }
}

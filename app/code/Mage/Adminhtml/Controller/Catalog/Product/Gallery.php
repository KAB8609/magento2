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
 * Catalog product gallery controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Controller_Catalog_Product_Gallery extends Mage_Adminhtml_Controller_Action
{
    public function uploadAction()
    {
        try {
            $uploader = Mage::getModel('Mage_Core_Model_File_Uploader', array('fileId' => 'image'));
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $imageAdapter = $this->_objectManager->get('Mage_Core_Model_Image_AdapterFactory')->create();
            $uploader->addValidateCallback('catalog_product_image', $imageAdapter, 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save(
                Mage::getSingleton('Mage_Catalog_Model_Product_Media_Config')->getBaseTmpMediaPath()
            );

            $this->_eventManager->dispatch('catalog_product_gallery_upload_image_after', array(
                'result' => $result,
                'action' => $this
            ));

            unset($result['tmp_name']);
            unset($result['path']);

            $result['url'] = Mage::getSingleton('Mage_Catalog_Model_Product_Media_Config')
                ->getTmpMediaUrl($result['file']);
            $result['file'] = $result['file'] . '.tmp';

        } catch (Exception $e) {
            $result = array(
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()
            );
        }

        $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result));
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_Catalog::products');
    }
} // Class Mage_Adminhtml_Controller_Catalog_Product_Gallery End
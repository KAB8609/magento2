<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product gallery
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Product_Gallery extends Magento_Core_Block_Template
{
    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->getProduct()->getMetaTitle());
        }
        return parent::_prepareLayout();
    }

    public function getProduct()
    {
        return Mage::registry('product');
    }

    public function getGalleryCollection()
    {
        return $this->getProduct()->getMediaGalleryImages();
    }

    public function getCurrentImage()
    {
        $imageId = $this->getRequest()->getParam('image');
        $image = null;
        if ($imageId) {
            $image = $this->getGalleryCollection()->getItemById($imageId);
        }

        if (!$image) {
            $image = $this->getGalleryCollection()->getFirstItem();
        }
        return $image;
    }

    public function getImageUrl()
    {
        return $this->getCurrentImage()->getUrl();
    }

    public function getImageFile()
    {
        return $this->getCurrentImage()->getFile();
    }

    /**
     * Retrieve image width
     *
     * @return bool|int
     */
    public function getImageWidth()
    {
        $file = $this->getCurrentImage()->getPath();
        if ($this->_filesystem->isFile($file)) {
            $size = getimagesize($file);
            if (isset($size[0])) {
                if ($size[0] > 600) {
                    return 600;
                } else {
                    return $size[0];
                }
            }
        }

        return false;
    }

    public function getPreviusImage()
    {
        $current = $this->getCurrentImage();
        if (!$current) {
            return false;
        }
        $previus = false;
        foreach ($this->getGalleryCollection() as $image) {
            if ($image->getValueId() == $current->getValueId()) {
                return $previus;
            }
            $previus = $image;
        }
        return $previus;
    }

    public function getNextImage()
    {
        $current = $this->getCurrentImage();
        if (!$current) {
            return false;
        }

        $next = false;
        $currentFind = false;
        foreach ($this->getGalleryCollection() as $image) {
            if ($currentFind) {
                return $image;
            }
            if ($image->getValueId() == $current->getValueId()) {
                $currentFind = true;
            }
        }
        return $next;
    }

    public function getPreviusImageUrl()
    {
        if ($image = $this->getPreviusImage()) {
            return $this->getUrl('*/*/*', array('_current'=>true, 'image'=>$image->getValueId()));
        }
        return false;
    }

    public function getNextImageUrl()
    {
        if ($image = $this->getNextImage()) {
            return $this->getUrl('*/*/*', array('_current'=>true, 'image'=>$image->getValueId()));
        }
        return false;
    }
}
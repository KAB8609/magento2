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
 * Catalog product media api
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product\Attribute\Media;

class Api extends \Magento\Catalog\Model\Api\Resource
{
    /**
     * Attribute code for media gallery
     */
    const ATTRIBUTE_CODE = 'media_gallery';

    /**
     * Allowed mime types for image
     *
     * @var array
     */
    protected $_mimeTypes = array(
        'image/jpeg' => 'jpg',
        'image/gif'  => 'gif',
        'image/png'  => 'png'
    );

    /**
     * @var \Magento\Filesystem
     */
    private $_filesystem;

    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $_mediaConfig;

    /**
     * @var \Magento\Core\Model\Image\Factory
     */
    protected $_imageFactory;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     * @param \Magento\Core\Model\Image\Factory $imageFactory
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Magento\Core\Model\Image\Factory $imageFactory
    ) {
        $this->_filesystem = $filesystem;
        $this->_filesystem->setIsAllowCreateDirectories(true);
        $this->_mediaConfig = $mediaConfig;
        $this->_storeIdSessionField = 'product_store_id';
        $this->_imageFactory = $imageFactory;
    }

    /**
     * Retrieve images for product
     *
     * @param int|string $productId
     * @param string|int $store
     * @return array
     */
    public function items($productId, $store = null, $identifierType = null)
    {
        $product = $this->_initProduct($productId, $store, $identifierType);

        $gallery = $this->_getGalleryAttribute($product);

        $galleryData = $product->getData(self::ATTRIBUTE_CODE);

        if (!isset($galleryData['images']) || !is_array($galleryData['images'])) {
            return array();
        }

        $result = array();

        foreach ($galleryData['images'] as &$image) {
            $result[] = $this->_imageToArray($image, $product);
        }

        return $result;
    }

    /**
     * Retrieve image data
     *
     * @param int|string $productId
     * @param string $file
     * @param string|int $store
     * @return array
     */
    public function info($productId, $file, $store = null, $identifierType = null)
    {
        $product = $this->_initProduct($productId, $store, $identifierType);

        $gallery = $this->_getGalleryAttribute($product);

        if (!$image = $gallery->getBackend()->getImage($product, $file)) {
            $this->_fault('not_exists');
        }

        return $this->_imageToArray($image, $product);
    }

    /**
     * Create new image for product and return image filename
     *
     * @throws \Magento\Api\Exception
     * @param int|string $productId
     * @param array $data
     * @param string|int $store
     * @return string
     */
    public function create($productId, $data, $store = null, $identifierType = null)
    {
        $data = $this->_prepareImageData($data);

        $product = $this->_initProduct($productId, $store, $identifierType);

        $gallery = $this->_getGalleryAttribute($product);

        if (!isset($data['file']) || !isset($data['file']['mime']) || !isset($data['file']['content'])) {
            $this->_fault('data_invalid', __('The image is not specified.'));
        }

        if (!isset($this->_mimeTypes[$data['file']['mime']])) {
            $this->_fault('data_invalid', __('Please correct the image type.'));
        }

        $fileContent = @base64_decode($data['file']['content'], true);
        if (!$fileContent) {
            $this->_fault('data_invalid', __('The image contents is not valid base64 data.'));
        }

        unset($data['file']['content']);

        $tmpDirectory = $this->_mediaConfig->getBaseTmpMediaPath() . DS . microtime();

        $fileName = !empty($data['file']['name']) ? $data['file']['name'] : 'image';
        $fileExtension = $this->_mimeTypes[$data['file']['mime']];
        $fileName = $tmpDirectory . DS . $fileName . '.' . $fileExtension;

        try {
            // Write image file
            $this->_filesystem->write($fileName, $fileContent);
            unset($fileContent);

            // try to create Image object - it fails with \Exception if image is not supported
            try {
                $this->_imageFactory->create($fileName);
            } catch (\Exception $e) {
                // Remove temporary directory
                $this->_filesystem->delete($tmpDirectory);
                throw new \Magento\Core\Exception($e->getMessage());
            }

            // Adding image to gallery
            $file = $gallery->getBackend()->addImage($product, $fileName, null, true);

            // Remove temporary directory
            $this->_filesystem->delete($tmpDirectory);

            $gallery->getBackend()->updateImage($product, $file, $data);

            if (isset($data['types'])) {
                $gallery->getBackend()->setMediaAttribute($product, $data['types'], $file);
            }

            $product->save();
        } catch (\Magento\Core\Exception $e) {
            $this->_fault('not_created', $e->getMessage());
        } catch (\Exception $e) {
            $this->_fault('not_created', __('We can\'t create the image.'));
        }

        return $gallery->getBackend()->getRenamedImage($file);
    }

    /**
     * Update image data
     *
     * @param int|string $productId
     * @param string $file
     * @param array $data
     * @param string|int $store
     * @return boolean
     */
    public function update($productId, $file, $data, $store = null, $identifierType = null)
    {
        $data = $this->_prepareImageData($data);

        $product = $this->_initProduct($productId, $store, $identifierType);

        $gallery = $this->_getGalleryAttribute($product);

        if (!$gallery->getBackend()->getImage($product, $file)) {
            $this->_fault('not_exists');
        }

        if (isset($data['file']['mime']) && isset($data['file']['content'])) {
            if (!isset($this->_mimeTypes[$data['file']['mime']])) {
                $this->_fault('data_invalid', __('Please correct the image type.'));
            }

            $fileContent = @base64_decode($data['file']['content'], true);
            if (!$fileContent) {
                $this->_fault('data_invalid', __('The image content is not valid base64 data.'));
            }

            unset($data['file']['content']);

            try {
                $fileName = $this->_mediaConfig->getMediaPath($file);
                $this->_filesystem->write($fileName, $fileContent);
            } catch (\Exception $e) {
                $this->_fault('not_created', __('We can\'t create the image.'));
            }
        }

        $gallery->getBackend()->updateImage($product, $file, $data);

        if (isset($data['types']) && is_array($data['types'])) {
            $oldTypes = array();
            foreach ($product->getMediaAttributes() as $attribute) {
                if ($product->getData($attribute->getAttributeCode()) == $file) {
                     $oldTypes[] = $attribute->getAttributeCode();
                }
            }

            $clear = array_diff($oldTypes, $data['types']);

            if (count($clear) > 0) {
                $gallery->getBackend()->clearMediaAttribute($product, $clear);
            }

            $gallery->getBackend()->setMediaAttribute($product, $data['types'], $file);
        }

        try {
            $product->save();
        } catch (\Magento\Core\Exception $e) {
            $this->_fault('not_updated', $e->getMessage());
        }

        return true;
    }

    /**
     * Remove image from product
     *
     * @param int|string $productId
     * @param string $file
     * @return boolean
     */
    public function remove($productId, $file, $identifierType = null)
    {
        $product = $this->_initProduct($productId, null, $identifierType);

        $gallery = $this->_getGalleryAttribute($product);

        if (!$gallery->getBackend()->getImage($product, $file)) {
            $this->_fault('not_exists');
        }

        $gallery->getBackend()->removeImage($product, $file);

        try {
            $product->save();
        } catch (\Magento\Core\Exception $e) {
            $this->_fault('not_removed', $e->getMessage());
        }

        return true;
    }


    /**
     * Retrieve image types (image, small_image, thumbnail, etc...)
     *
     * @param int $setId
     * @return array
     */
    public function types($setId)
    {
        $attributes = \Mage::getModel('\Magento\Catalog\Model\Product')->getResource()
                ->loadAllAttributes()
                ->getSortedAttributes($setId);

        $result = array();

        foreach ($attributes as $attribute) {
            /* @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
            if ($attribute->isInSet($setId)
                && $attribute->getFrontendInput() == 'media_image') {
                if ($attribute->isScopeGlobal()) {
                    $scope = 'global';
                } elseif ($attribute->isScopeWebsite()) {
                    $scope = 'website';
                } else {
                    $scope = 'store';
                }

                $result[] = array(
                    'code'         => $attribute->getAttributeCode(),
                    'scope'        => $scope
                );
            }
        }

        return $result;
    }

    /**
     * Prepare data to create or update image
     *
     * @param array $data
     * @return array
     */
    protected function _prepareImageData($data)
    {
        return $data;
    }

    /**
     * Retrieve gallery attribute from product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Resource\Attribute|boolean
     */
    protected function _getGalleryAttribute($product)
    {
        $attributes = $product->getTypeInstance()
            ->getSetAttributes($product);

        if (!isset($attributes[self::ATTRIBUTE_CODE])) {
            $this->_fault('not_media');
        }

        return $attributes[self::ATTRIBUTE_CODE];
    }

    /**
     * Retrie
     * ve media config
     *
     * @return \Magento\Catalog\Model\Product\Media\Config
     */
    protected function _getMediaConfig()
    {
        return \Mage::getSingleton('Magento\Catalog\Model\Product\Media\Config');
    }

    /**
     * Converts image to api array data
     *
     * @param array $image
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    protected function _imageToArray(&$image, $product)
    {
        $result = array(
            'file'      => $image['file'],
            'label'     => $image['label'],
            'position'  => $image['position'],
            'exclude'   => $image['disabled'],
            'url'       => $this->_getMediaConfig()->getMediaUrl($image['file']),
            'types'     => array()
        );


        foreach ($product->getMediaAttributes() as $attribute) {
            if ($product->getData($attribute->getAttributeCode()) == $image['file']) {
                $result['types'][] = $attribute->getAttributeCode();
            }
        }

        return $result;
    }

    /**
     * Retrieve product
     *
     * @param int|string $productId
     * @param string|int $store
     * @param  string $identifierType
     * @return \Magento\Catalog\Model\Product
     */
    protected function _initProduct($productId, $store = null, $identifierType = null)
    {
        $product = \Mage::helper('Magento\Catalog\Helper\Product')->getProduct($productId, $this->_getStoreId($store), $identifierType);
        if (!$product->getId()) {
            $this->_fault('product_not_exists');
        }

        return $product;
    }
} // Class \Magento\Catalog\Model\Product\Attribute\Media\Api End

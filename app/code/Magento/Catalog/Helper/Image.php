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
 * Catalog image helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Helper_Image extends Magento_Core_Helper_Abstract
{
    /**
     * Current model
     *
     * @var Magento_Catalog_Model_Product_Image
     */
    protected $_model;

    /**
     * Scheduled for resize image
     *
     * @var bool
     */
    protected $_scheduleResize = false;

    /**
     * Scheduled for rotate image
     *
     * @var bool
     */
    protected $_scheduleRotate = false;

    /**
     * Angle
     *
     * @var int
     */
    protected $_angle;

    /**
     * Watermark file name
     *
     * @var string
     */
    protected $_watermark;

    /**
     * Watermark Position
     *
     * @var string
     */
    protected $_watermarkPosition;

    /**
     * Watermark Size
     *
     * @var string
     */
    protected $_watermarkSize;

    /**
     * Watermark Image opacity
     *
     * @var int
     */
    protected $_watermarkImageOpacity;

    /**
     * Current Product
     *
     * @var Magento_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Image File
     *
     * @var string
     */
    protected $_imageFile;

    /**
     * Image Placeholder
     *
     * @var string
     */
    protected $_placeholder;

    /**
     * @var Magento_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_View_Url $viewUrl
     */
    public function __construct(Magento_Core_Helper_Context $context, Magento_Core_Model_View_Url $viewUrl)
    {
        parent::__construct($context);
        $this->_viewUrl = $viewUrl;
    }

    /**
     * Reset all previous data
     *
     * @return Magento_Catalog_Helper_Image
     */
    protected function _reset()
    {
        $this->_model = null;
        $this->_scheduleResize = false;
        $this->_scheduleRotate = false;
        $this->_angle = null;
        $this->_watermark = null;
        $this->_watermarkPosition = null;
        $this->_watermarkSize = null;
        $this->_watermarkImageOpacity = null;
        $this->_product = null;
        $this->_imageFile = null;
        return $this;
    }

    /**
     * Initialize Helper to work with Image
     *
     * @param Magento_Catalog_Model_Product $product
     * @param string $attributeName
     * @param mixed $imageFile
     * @return Magento_Catalog_Helper_Image
     */
    public function init(Magento_Catalog_Model_Product $product, $attributeName, $imageFile=null)
    {
        $this->_reset();
        $this->_setModel(Mage::getModel('Magento_Catalog_Model_Product_Image'));
        $this->_getModel()->setDestinationSubdir($attributeName);
        $this->setProduct($product);

        $this->setWatermark(
            Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_image")
        );
        $this->setWatermarkImageOpacity(
            Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_imageOpacity")
        );
        $this->setWatermarkPosition(
            Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_position")
        );
        $this->setWatermarkSize(
            Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_size")
        );

        if ($imageFile) {
            $this->setImageFile($imageFile);
        } else {
            // add for work original size
            $this->_getModel()->setBaseFile($this->getProduct()->getData($this->_getModel()->getDestinationSubdir()));
        }
        return $this;
    }

    /**
     * Schedule resize of the image
     * $width *or* $height can be null - in this case, lacking dimension will be calculated.
     *
     * @see Magento_Catalog_Model_Product_Image
     * @param int $width
     * @param int $height
     * @return Magento_Catalog_Helper_Image
     */
    public function resize($width, $height = null)
    {
        $this->_getModel()->setWidth($width)->setHeight($height);
        $this->_scheduleResize = true;
        return $this;
    }

    /**
     * Set image quality, values in percentage from 0 to 100
     *
     * @param int $quality
     * @return Magento_Catalog_Helper_Image
     */
    public function setQuality($quality)
    {
        $this->_getModel()->setQuality($quality);
        return $this;
    }

    /**
     * Guarantee, that image picture width/height will not be distorted.
     * Applicable before calling resize()
     * It is true by default.
     *
     * @see Magento_Catalog_Model_Product_Image
     * @param bool $flag
     * @return Magento_Catalog_Helper_Image
     */
    public function keepAspectRatio($flag)
    {
        $this->_getModel()->setKeepAspectRatio($flag);
        return $this;
    }

    /**
     * Guarantee, that image will have dimensions, set in $width/$height
     * Applicable before calling resize()
     * Not applicable, if keepAspectRatio(false)
     *
     * $position - TODO, not used for now - picture position inside the frame.
     *
     * @see Magento_Catalog_Model_Product_Image
     * @param bool $flag
     * @param array $position
     * @return Magento_Catalog_Helper_Image
     */
    public function keepFrame($flag, $position = array('center', 'middle'))
    {
        $this->_getModel()->setKeepFrame($flag);
        return $this;
    }

    /**
     * Guarantee, that image will not lose transparency if any.
     * Applicable before calling resize()
     * It is true by default.
     *
     * $alphaOpacity - TODO, not used for now
     *
     * @see Magento_Catalog_Model_Product_Image
     * @param bool $flag
     * @param int $alphaOpacity
     * @return Magento_Catalog_Helper_Image
     */
    public function keepTransparency($flag, $alphaOpacity = null)
    {
        $this->_getModel()->setKeepTransparency($flag);
        return $this;
    }

    /**
     * Guarantee, that image picture will not be bigger, than it was.
     * Applicable before calling resize()
     * It is false by default
     *
     * @param bool $flag
     * @return Magento_Catalog_Helper_Image
     */
    public function constrainOnly($flag)
    {
        $this->_getModel()->setConstrainOnly($flag);
        return $this;
    }

    /**
     * Set color to fill image frame with.
     * Applicable before calling resize()
     * The keepTransparency(true) overrides this (if image has transparent color)
     * It is white by default.
     *
     * @see Magento_Catalog_Model_Product_Image
     * @param array $colorRGB
     * @return Magento_Catalog_Helper_Image
     */
    public function backgroundColor($colorRGB)
    {
        // assume that 3 params were given instead of array
        if (!is_array($colorRGB)) {
            $colorRGB = func_get_args();
        }
        $this->_getModel()->setBackgroundColor($colorRGB);
        return $this;
    }

    /**
     * Rotate image into specified angle
     *
     * @param int $angle
     * @return Magento_Catalog_Helper_Image
     */
    public function rotate($angle)
    {
        $this->setAngle($angle);
        $this->_getModel()->setAngle($angle);
        $this->_scheduleRotate = true;
        return $this;
    }

    /**
     * Add watermark to image
     * size param in format 100x200
     *
     * @param string $fileName
     * @param string $position
     * @param string $size
     * @param int $imageOpacity
     * @return Magento_Catalog_Helper_Image
     */
    public function watermark($fileName, $position, $size=null, $imageOpacity=null)
    {
        $this->setWatermark($fileName)
            ->setWatermarkPosition($position)
            ->setWatermarkSize($size)
            ->setWatermarkImageOpacity($imageOpacity);
        return $this;
    }

    /**
     * Set placeholder
     *
     * @param string $fileName
     * @return void
     */
    public function placeholder($fileName)
    {
        $this->_placeholder = $fileName;
    }

    /**
     * Get Placeholder
     *
     * @return string
     */
    public function getPlaceholder()
    {
        if (!$this->_placeholder) {
            $attr = $this->_getModel()->getDestinationSubdir();
            $this->_placeholder = 'Magento_Catalog::images/product/placeholder/' . $attr . '.jpg';
        }
        return $this->_placeholder;
    }

    /**
     * Return Image URL
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $model = $this->_getModel();

            if ($this->getImageFile()) {
                $model->setBaseFile($this->getImageFile());
            } else {
                $model->setBaseFile($this->getProduct()->getData($model->getDestinationSubdir()));
            }

            if ($model->isCached()) {
                return $model->getUrl();
            } else {
                if ($this->_scheduleRotate) {
                    $model->rotate($this->getAngle());
                }

                if ($this->_scheduleResize) {
                    $model->resize();
                }

                if ($this->getWatermark()) {
                    $model->setWatermark($this->getWatermark());
                }

                $url = $model->saveFile()->getUrl();
            }
        } catch (Exception $e) {
            $url = $this->_viewUrl->getViewFileUrl($this->getPlaceholder());
        }
        return $url;
    }

    /**
     * Set current Image model
     *
     * @param Magento_Catalog_Model_Product_Image $model
     * @return Magento_Catalog_Helper_Image
     */
    protected function _setModel($model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * Get current Image model
     *
     * @return Magento_Catalog_Model_Product_Image
     */
    protected function _getModel()
    {
        return $this->_model;
    }

    /**
     * Set Rotation Angle
     *
     * @param int $angle
     * @return Magento_Catalog_Helper_Image
     */
    protected function setAngle($angle)
    {
        $this->_angle = $angle;
        return $this;
    }

    /**
     * Get Rotation Angle
     *
     * @return int
     */
    protected function getAngle()
    {
        return $this->_angle;
    }

    /**
     * Set watermark file name
     *
     * @param string $watermark
     * @return Magento_Catalog_Helper_Image
     */
    protected function setWatermark($watermark)
    {
        $this->_watermark = $watermark;
        $this->_getModel()->setWatermarkFile($watermark);
        return $this;
    }

    /**
     * Get watermark file name
     *
     * @return string
     */
    protected function getWatermark()
    {
        return $this->_watermark;
    }

    /**
     * Set watermark position
     *
     * @param string $position
     * @return Magento_Catalog_Helper_Image
     */
    protected function setWatermarkPosition($position)
    {
        $this->_watermarkPosition = $position;
        $this->_getModel()->setWatermarkPosition($position);
        return $this;
    }

    /**
     * Get watermark position
     *
     * @return string
     */
    protected function getWatermarkPosition()
    {
        return $this->_watermarkPosition;
    }

    /**
     * Set watermark size
     * param size in format 100x200
     *
     * @param string $size
     * @return Magento_Catalog_Helper_Image
     */
    public function setWatermarkSize($size)
    {
        $this->_watermarkSize = $size;
        $this->_getModel()->setWatermarkSize($this->parseSize($size));
        return $this;
    }

    /**
     * Get watermark size
     *
     * @return string
     */
    protected function getWatermarkSize()
    {
        return $this->_watermarkSize;
    }

    /**
     * Set watermark image opacity
     *
     * @param int $imageOpacity
     * @return Magento_Catalog_Helper_Image
     */
    public function setWatermarkImageOpacity($imageOpacity)
    {
        $this->_watermarkImageOpacity = $imageOpacity;
        $this->_getModel()->setWatermarkImageOpacity($imageOpacity);
        return $this;
    }

    /**
     * Get watermark image opacity
     *
     * @return int
     */
    protected function getWatermarkImageOpacity()
    {
        if ($this->_watermarkImageOpacity) {
            return $this->_watermarkImageOpacity;
        }

        return $this->_getModel()->getWatermarkImageOpacity();
    }

    /**
     * Set current Product
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_Catalog_Helper_Image
     */
    protected function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * Get current Product
     *
     * @return Magento_Catalog_Model_Product
     */
    protected function getProduct()
    {
        return $this->_product;
    }

    /**
     * Set Image file
     *
     * @param string $file
     * @return Magento_Catalog_Helper_Image
     */
    protected function setImageFile($file)
    {
        $this->_imageFile = $file;
        return $this;
    }

    /**
     * Get Image file
     *
     * @return string
     */
    protected function getImageFile()
    {
        return $this->_imageFile;
    }

    /**
     * Retrieve size from string
     *
     * @param string $string
     * @return array|bool
     */
    protected function parseSize($string)
    {
        $size = explode('x', strtolower($string));
        if (sizeof($size) == 2) {
            return array(
                'width' => ($size[0] > 0) ? $size[0] : null,
                'heigth' => ($size[1] > 0) ? $size[1] : null,
            );
        }
        return false;
    }

    /**
     * Retrieve original image width
     *
     * @return int|null
     */
    public function getOriginalWidth()
    {
        return $this->_getModel()->getImageProcessor()->getOriginalWidth();
    }

    /**
     * Retrieve original image height
     *
     * @return int|null
     */
    public function getOriginalHeight()
    {
        return $this->_getModel()->getImageProcessor()->getOriginalHeight();
    }

    /**
     * Retrieve Original image size as array
     * 0 - width, 1 - height
     *
     * @return array
     */
    public function getOriginalSizeArray()
    {
        return array(
            $this->getOriginalWidth(),
            $this->getOriginalHeight()
        );
    }
}
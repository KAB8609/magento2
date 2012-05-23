<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Image
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Varien_Image_Adapter_ImageMagick extends Varien_Image_Adapter_Abstract
{
    /**
     * The blur factor where > 1 is blurry, < 1 is sharp
     */
    const BLUR_FACTOR = 0.7;

    /**
     * Options Container
     *
     * @var array
     */
    protected $_options = array(
        'resolution' => array(
            'x' => 72,
            'y' => 72
        ),
        'small_image' => array(
            'width'  => 300,
            'height' => 300
        ),
        'sharpen' => array(
            'radius'    => 4,
            'deviation' => 1
        )
    );

    /**
     * Set/get background color. Check Imagick::COLOR_* constants
     *
     * @param int|string|array $color
     * @return int
     */
    public function backgroundColor($color = null)
    {
        if ($color) {
            if (is_array($color)) {
                $color = "rgb(" . join(',', $color) . ")";
            }

            $pixel = new ImagickPixel;
            if (is_numeric($color)) {
                $pixel->setColorValue($color, 1);
            } else {
                $pixel->setColor($color);
            }
            if ($this->_imageHandler) {
                $this->_imageHandler->setImageBackgroundColor($color);
            }
        } else {
            $pixel = $this->_imageHandler->getImageBackgroundColor();
        }

        $this->imageBackgroundColor = $pixel->getColorAsString();

        return $this->imageBackgroundColor;
    }

    /**
     * Open image for processing
     *
     * @throws RuntimeException if image format is unsupported
     * @param string $filename
     */
    public function open($filename)
    {
        $this->_fileName = $filename;
        $this->_getFileAttributes();

        try {
            $this->_imageHandler = new Imagick($this->_fileName);
        } catch (ImagickException $e) {
            throw new RuntimeException('Unsupported image format.', $e->getCode(), $e);
        }

        $this->backgroundColor();
        $this->getMimeType();
    }

    /**
     * Save image to specific path.
     * If some folders of path does not exist they will be created
     *
     * @throws Exception  if destination path is not writable
     * @param string $destination
     * @param string $newName
     */
    public function save($destination = null, $newName = null)
    {
        $fileName = $this->_prepareDestination($destination, $newName);

        $this->_applyOptions();
        $this->_imageHandler->stripImage();
        $this->_imageHandler->writeImage($fileName);
    }

    /**
     * Apply options to image. Will be usable later when create an option container
     *
     * @return Varien_Image_Adapter_ImageMagick
     */
    protected function _applyOptions()
    {
        $this->_imageHandler->setImageCompressionQuality($this->quality());
        $this->_imageHandler->setImageCompression(Imagick::COMPRESSION_JPEG);
        $this->_imageHandler->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);
        $this->_imageHandler->setImageResolution(
            $this->_options['resolution']['x'],
            $this->_options['resolution']['y']
        );
        if (method_exists($this->_imageHandler, 'optimizeImageLayers')) {
            $this->_imageHandler->optimizeImageLayers();
        }

        return $this;
    }

    /**
     * Put image into output stream
     *
     */
    public function display()
    {
        header("Content-type: " . $this->getMimeType());
        $this->_applyOptions();
        echo (string)$this->_imageHandler;
    }

    /**
     * Change the image size
     *
     * @param int $frameWidth
     * @param int $frameHeight
     */
    public function resize($frameWidth = null, $frameHeight = null)
    {
        $dims = $this->_adaptResizeValues($frameWidth, $frameHeight);

        if ($dims['dst']['width'] > $this->_imageHandler->getImageWidth() ||
            $dims['dst']['height'] > $this->_imageHandler->getImageHeight()
        ) {
            $this->_imageHandler->sampleImage($dims['dst']['width'], $dims['dst']['height']);
        } else {
            $this->_imageHandler->resizeImage(
                $dims['dst']['width'],
                $dims['dst']['height'],
                Imagick::FILTER_LANCZOS,
                self::BLUR_FACTOR,
                true
            );
        }

        if ($this->_imageHandler->getImageWidth() < $this->_options['small_image']['width']
            || $this->_imageHandler->getImageHeight() < $this->_options['small_image']['height']
        ) {
            $this->_imageHandler->sharpenImage(
                $this->_options['sharpen']['radius'],
                $this->_options['sharpen']['deviation']
            );
        }

        $this->refreshImageDimensions();
    }

    /**
     * Rotate image on specific angle
     *
     * @param int $angle
     */
    public function rotate($angle)
    {
        // compatibility with GD2 adapter
        $angle = 360 - $angle;
        $pixel = new ImagickPixel;
        $pixel->setColor("rgb(" . $this->imageBackgroundColor . ")");

        $this->_imageHandler->rotateImage($pixel, $angle);
        $this->refreshImageDimensions();
    }

    /**
     * Crop image
     *
     * @param int $top
     * @param int $left
     * @param int $right
     * @param int $bottom
     * @return bool
     */
    public function crop($top = 0, $left = 0, $right = 0, $bottom = 0)
    {
        if ($left == 0 && $top == 0 && $right == 0 && $bottom == 0) {
            return false;
        }

        $newWidth  = $this->_imageSrcWidth  - $left - $right;
        $newHeight = $this->_imageSrcHeight - $top  - $bottom;

        $this->_imageHandler->cropImage($newWidth, $newHeight, $left, $top);
        $this->refreshImageDimensions();
        return true;
    }

    /**
     * Add watermark to image
     *
     * @throws RuntimeException
     * @param string $imagePath
     * @param int $positionX
     * @param int $positionY
     * @param int $watermarkImageOpacity
     * @param bool $isWaterMarkTile
     */
    public function watermark($imagePath, $positionX = 0, $positionY = 0, $opacity = 30, $isWaterMarkTile = false)
    {
        $opacity = $this->getWatermarkImageOpacity()
            ? $this->getWatermarkImageOpacity()
            : $opacity;

        $opacity = (float)number_format($opacity / 100, 1);
        $watermark = new Imagick($imagePath);

        $iterator = $watermark->getPixelIterator();

        if (method_exists($watermark, 'setImageOpacity')) {
            // available from imagick 6.2.9
            $watermark->setImageOpacity($opacity);
        } else {
            // go to each pixel and make it transparent
            foreach ($iterator as $y => $pixels) {
                foreach ($pixels as $x => $pixel) {
                    $watermark->paintTransparentImage($pixel, $opacity, 65535);
                }
                $iterator->syncIterator();
            }
        }

        switch ($this->getWatermarkPosition()) {
            case self::POSITION_STRETCH:
                $watermark->sampleImage($this->_imageSrcWidth, $this->_imageSrcHeight);
                break;
            case self::POSITION_CENTER:
                $positionX = ($this->_imageSrcWidth  - $watermark->getImageWidth())/2;
                $positionY = ($this->_imageSrcHeight - $watermark->getImageHeight())/2;
                break;
            case self::POSITION_TOP_RIGHT:
                $positionX = $this->_imageSrcWidth - $watermark->getImageWidth();
                break;
            case self::POSITION_BOTTOM_RIGHT:
                $positionX = $this->_imageSrcWidth  - $watermark->getImageWidth();
                $positionY = $this->_imageSrcHeight - $watermark->getImageHeight();
                break;
            case self::POSITION_BOTTOM_LEFT:
                $positionY = $this->_imageSrcHeight - $watermark->getImageHeight();
                break;
            case self::POSITION_TILE:
                $isWaterMarkTile = true;
                break;
        }

        try {
            if ($isWaterMarkTile) {
                $offsetX = $positionX;
                $offsetY = $positionY;
                while($offsetY <= ($this->_imageSrcHeight + $watermark->getImageHeight())) {
                    while($offsetX <= ($this->_imageSrcWidth + $watermark->getImageWidth())) {
                        $this->_imageHandler->compositeImage(
                            $watermark,
                            Imagick::COMPOSITE_OVER,
                            $offsetX,
                            $offsetY
                        );
                        $offsetX += $watermark->getImageWidth();
                    }
                    $offsetX = $positionX;
                    $offsetY += $watermark->getImageHeight();
                }
            } else {
                $this->_imageHandler->compositeImage(
                    $watermark,
                    Imagick::COMPOSITE_OVER,
                    $positionX,
                    $positionY
                );
            }
        } catch (ImagickException $e) {
            throw new RuntimeException('Unable to create watermark.', $e->getCode(), $e);
        }

        // merge layers
        $this->_imageHandler->flattenImages();
        $watermark->destroy();
    }

    /**
     * Checks required dependecies
     *
     * @throws Exception if some of dependecies are missing
     */
    public function checkDependencies()
    {
        if (!class_exists('Imagick', false)) {
            throw new Exception("Required PHP extension 'Imagick' was not loaded.");
        }
    }

    /**
     * Reassign image dimensions
     */
    private function refreshImageDimensions()
    {
        $this->_imageSrcWidth  = $this->_imageHandler->getImageWidth();
        $this->_imageSrcHeight = $this->_imageHandler->getImageHeight();
        $this->_imageHandler->setImagePage($this->_imageSrcWidth, $this->_imageSrcHeight, 0, 0);
    }

    /**
     * Standard destructor. Destroy stored information about image
     *
     */
    public function __destruct()
    {
        $this->destroy();
    }

    /**
     * Destroy stored information about image
     *
     * @return Varien_Image_Adapter_ImageMagick
     */
    public function destroy()
    {
        if (null !== $this->_imageHandler && $this->_imageHandler instanceof Imagick) {
            $this->_imageHandler->clear();
            $this->_imageHandler->destroy();
            $this->_imageHandler = null;
        }
        return $this;
    }

    /**
     * Returns rgb array of the specified pixel
     *
     * @param int $x
     * @param int $y
     * @return array
     */
    public function getColorAt($x, $y)
    {
        $pixel = $this->_imageHandler->getImagePixelColor($x, $y);

        return explode(',', $pixel->getColorAsString());
    }
}

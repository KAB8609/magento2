<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Image\Adapter;

interface AdapterInterface
{
    /**
     * Adapter type
     */
    const ADAPTER_GD2   = 'GD2';
    const ADAPTER_IM    = 'IMAGEMAGICK';

    /**
     * Returns rgba array of the specified pixel
     *
     * @param int $x
     * @param int $y
     * @return array
     */
    public function getColorAt($x, $y);

    /**
     * @see \Magento\Image\Adapter\AbstractAdapter::getImage
     * @return string
     */
    public function getImage();

    /**
     * Add watermark to image
     *
     * @param string $imagePath
     * @param int $positionX
     * @param int $positionY
     * @param int $opacity
     * @param bool $tile
     */
    public function watermark($imagePath, $positionX = 0, $positionY = 0, $opacity = 30, $tile = false);

    /**
     * Reassign image dimensions
     */
    public function refreshImageDimensions();

    /**
     * Checks required dependencies
     *
     * @throws \Exception if some of dependencies are missing
     */
    public function checkDependencies();

    /**
     * Create Image from string
     *
     * @param string $text
     * @param string $font
     * @return \Magento\Image\Adapter\AbstractAdapter
     */
    public function createPngFromString($text, $font = '');

    /**
     * Open image for processing
     *
     * @param string $filename
     */
    public function open($filename);

    /**
     * Change the image size
     *
     * @param int $frameWidth
     * @param int $frameHeight
     */
    public function resize($frameWidth = null, $frameHeight = null);

    /**
     * Crop image
     *
     * @param int $top
     * @param int $left
     * @param int $right
     * @param int $bottom
     * @return bool
     */
    public function crop($top = 0, $left = 0, $right = 0, $bottom = 0);

    /**
     * Save image to specific path.
     * If some folders of path does not exist they will be created
     *
     * @throws \Exception  if destination path is not writable
     * @param string $destination
     * @param string $newName
     */
    public function save($destination = null, $newName = null);

    /**
     * Rotate image on specific angle
     *
     * @param int $angle
     */
    public function rotate($angle);
}
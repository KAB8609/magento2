<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\File;

interface ReadInterface
{
    /**
     * Reads the specified number of bytes from the current position.
     *
     * @param int $length The number of bytes to read
     * @return string
     */
    public function read($length);

    /**
     * Reads one CSV row from the file
     *
     * @param int $length [optional] <p>
     * @param string $delimiter [optional]
     * @param string $enclosure [optional]
     * @param string $escape [optional]
     * @return array|bool false on end of file
     */
    public function readCsv($length = 0, $delimiter = ',', $enclosure = '"', $escape = '\\');

    /**
     * Returns the current position
     *
     * @return int
     */
    public function tell();

    /**
     * Seeks to the specified offset
     *
     * @param int $length
     * @param int $whence
     * @return int
     */
    public function seek($length, $whence = SEEK_SET);

    /**
     * Checks if the current position is the end-of-file
     *
     * @return bool
     */
    public function eof();

    /**
     * Closes the file.
     *
     * @return bool
     */
    public function close();
}
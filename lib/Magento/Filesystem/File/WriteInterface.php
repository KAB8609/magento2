<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\File;

interface WriteInterface extends ReadInterface
{
    /**
     * Writes the data to file.
     *
     * @param string $data
     * @return int
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function write($data);

    /**
     * Writes one CSV row to the file.
     *
     * @param array $data
     * @param string $delimiter
     * @param string $enclosure
     * @return int
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function writeCsv(array $data, $delimiter = ',', $enclosure = '"');

    /**
     * Flushes the output.
     *
     * @return bool
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function flush();

    /**
     * Portable advisory file locking
     *
     * @param bool $exclusive
     * @return bool
     */
    public function lock($exclusive = true);

    /**
     * File unlocking
     *
     * @return bool
     */
    public function unlock();
}
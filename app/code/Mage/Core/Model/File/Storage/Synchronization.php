<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Core_Model_File_Storage_Synchronization
{
    /**
     * Database storage factory
     *
     * @var Mage_Core_Model_File_Storage_DatabaseFactory
     */
    protected $_storageFactory;

    /**
     * File stream handler
     *
     * @var Magento_Io_File
     */
    protected $_streamFactory;

    /**
     * @param Mage_Core_Model_File_Storage_DatabaseFactory $storageFactory
     * @param Magento_Filesystem_Stream_LocalFactory $streamFactory
     */
    public function __construct(
        Mage_Core_Model_File_Storage_DatabaseFactory $storageFactory,
        Magento_Filesystem_Stream_LocalFactory $streamFactory
    ) {
        $this->_storageFactory = $storageFactory;
        $this->_streamFactory = $streamFactory;
    }

    /**
     * Synchronize file
     *
     * @param string $relativeFileName
     * @param string $filePath
     * @throws LogicException
     */
    public function synchronize($relativeFileName, $filePath)
    {
        /** @var $storage Mage_Core_Model_File_Storage_Database */
        $storage = $this->_storageFactory->create();
        try {
            $storage->loadByFilename($relativeFileName);
        } catch (Exception $e) {
        }
        if ($storage->getId()) {
            $directory = dirname($filePath);
            if (!is_dir($directory) && !mkdir($directory, 0777, true)) {
                throw new LogicException('Could not create directory');
            }

            /** @var Magento_Filesystem_StreamInterface $stream */
            $stream = $this->_streamFactory->create(array('path' => $filePath));
            try{
                $stream->open('w');
                $stream->lock(true);
                $stream->write($storage->getContent());
                $stream->unlock();
                $stream->close();
            } catch (Magento_Filesystem_Exception $e) {
                $stream->close();
            }
        }
    }
}

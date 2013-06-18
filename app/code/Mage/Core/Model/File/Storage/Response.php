<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Core_Model_File_Storage_Response
{
    /**
     * Application object manager
     *
     * @var Mage_Core_Model_ObjectManager
     */
    protected $_objectManager;

    public function __construct(Magento_ObjectManager $objectManager = null)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Send the file to client
     *
     * @param string|array $filePath
     */
    public function sendFile($filePath)
    {
        $transfer = $this->_objectManager->create('Varien_File_Transfer_Adapter_Http');
        $transfer->send($filePath);
    }

    /**
     * Return page header
     */
    public function sendNotFound()
    {
        header('HTTP/1.0 404 Not Found');
    }
}

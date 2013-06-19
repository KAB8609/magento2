<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Core_Model_File_Storage_Request
{
    /**
     * Path info
     *
     * @var string
     */
    protected $_pathInfo;

    /**
     * Requested file path
     *
     * @var string
     */
    protected $_filePath;

    /**
     * @param string $workingDir
     * @param Zend_Controller_Request_Http $request
     */
    public function __construct($workingDir, Zend_Controller_Request_Http $request = null)
    {
        $request = $request ? : new Zend_Controller_Request_Http();
        $this->_pathInfo = str_replace('..', '', ltrim($request->getPathInfo(), '/'));
        $this->_filePath = str_replace('/', DS, $workingDir . DS . $this->_pathInfo);
    }

    /**
     * Retrieve path info
     *
     * @return string
     */
    public function getPathInfo()
    {
        return $this->_pathInfo;
    }

    /**
     * Retrieve file path
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->_filePath;
    }
}

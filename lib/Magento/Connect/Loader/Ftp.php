<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class for ftp loader which using in the Rest
 *
 * @category    Mage
 * @package     Mage_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Connect_Loader_Ftp
{

    const TEMPORARY_DIR = 'var/package/tmp';

    const FTP_USER = 'magconnect';

    const FTP_PASS = '4SyTUxPts0o2';

    /**
    * Object of Ftp
    *
    * @var Magento_Connect_Ftp
    */
    protected $_ftp = null;

    /**
     * Response body
     * @var string
     */
    protected $_responseBody = '';

    /**
     * Response status
     * @var int
     */
    protected $_responseStatus = 0;

    /**
    * Constructor
    */
    public function __construct()
    {
        $this->_ftp = new Magento_Connect_Ftp();
    }

    public function getFtp()
    {
        return $this->_ftp;
    }

    /**
    * Retrieve file from URI
    *
    * @param mixed $uri
    * @return bool
    */
    public function get($uri)
    {
        $remoteFile = basename($uri);
        $uri = dirname($uri);
        $uri = str_replace('http://', '', $uri);
        $uri = str_replace('ftp://', '', $uri);
        $uri = self::FTP_USER.":".self::FTP_PASS."@".$uri;
        $this->getFtp()->connect("ftp://".$uri);
        $this->getFtp()->pasv(true);
        $localFile = self::TEMPORARY_DIR.DS.time().".xml";

        if ($this->getFtp()->get($localFile, $remoteFile)) {
            $this->_responseBody = file_get_contents($localFile);
            $this->_responseStatus = 200;
        }
        @unlink($localFile);
        $this->getFtp()->close();
        return $out;
    }

    /**
     * Get response status code
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->_responseStatus;
    }

    /**
    * put your comment there...
    *
    * @return string
    */
    public function getBody()
    {
        return $this->_responseBody;
    }

}
<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Custom Zend_Controller_Response_Http class (formally)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Controller_Response_Http extends Zend_Controller_Response_Http
{
    /**
     * Transport object for observers to perform
     *
     * @var Magento_Object
     */
    protected static $_transportObject = null;

    /**
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     */
    public function __construct(Magento_Core_Model_Event_Manager $eventManager)
    {
        $this->_eventManager = $eventManager;
    }

    /**
     * Fixes CGI only one Status header allowed bug
     *
     * @link  http://bugs.php.net/bug.php?id=36705
     *
     * @return Magento_Core_Controller_Response_Http
     */
    public function sendHeaders()
    {
        if (!$this->canSendHeaders()) {
            $this->_objectManager->get('Magento_Core_Model_Logger')
                ->log('HEADERS ALREADY SENT: '.mageDebugBacktrace(true, true, true));
            return $this;
        }

        if (substr(php_sapi_name(), 0, 3) == 'cgi') {
            $statusSent = false;
            foreach ($this->_headersRaw as $index => $header) {
                if (stripos($header, 'status:')===0) {
                    if ($statusSent) {
                        unset($this->_headersRaw[$index]);
                    } else {
                        $statusSent = true;
                    }
                }
            }
            foreach ($this->_headers as $index => $header) {
                if (strcasecmp($header['name'], 'status') === 0) {
                    if ($statusSent) {
                        unset($this->_headers[$index]);
                    } else {
                        $statusSent = true;
                    }
                }
            }
        }
        return parent::sendHeaders();
    }

    public function sendResponse()
    {
        return parent::sendResponse();
    }

    /**
     * Additionally check for session messages in several domains case
     *
     * @param string $url
     * @param int $code
     * @return Magento_Core_Controller_Response_Http
     */
    public function setRedirect($url, $code = 302)
    {
        /**
         * Use single transport object instance
         */
        if (self::$_transportObject === null) {
            self::$_transportObject = new Magento_Object;
        }
        self::$_transportObject->setUrl($url);
        self::$_transportObject->setCode($code);
        $this->_eventManager->dispatch('controller_response_redirect',
            array('response' => $this, 'transport' => self::$_transportObject));

        return parent::setRedirect(self::$_transportObject->getUrl(), self::$_transportObject->getCode());
    }

    /**
     * Get header value by name.
     * Returns first found header by passed name.
     * If header with specified name was not found returns false.
     *
     * @param string $name
     * @return array|bool
     */
    public function getHeader($name)
    {
        foreach ($this->_headers as $header) {
            if ($header['name'] == $name) {
                return $header;
            }
        }
        return false;
    }
}

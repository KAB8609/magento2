<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha image model
 *
 * @category   Mage
 * @package    Mage_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Used for "name" attribute of captcha's input field
     */
    const INPUT_NAME_FIELD_VALUE = 'captcha';

    /**
     * Always show captcha
     */
    const MODE_ALWAYS     = 'always';

    /**
     * Show captcha only after certain number of unsuccessful attempts
     */
    const MODE_AFTER_FAIL = 'after_fail';

    /**
     * Captcha fonts path
     */
    const XML_PATH_CAPTCHA_FONTS = 'default/captcha/fonts';

    /**
     * List uses Models of Captcha
     * @var array
     */
    protected $_captcha = array();

    /**
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Mage_Core_Model_Website
     */
    protected $_website;

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs = null;

    /**
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Translate $translator
     */
    public function __construct(Mage_Core_Model_Dir $dirs, Mage_Core_Model_Translate $translator)
    {
        parent::__construct($translator);
        $this->_dirs = $dirs;
    }

    /**
     * Get Config
     * @return Mage_Core_Model_Config
     */
    public function getConfig()
    {
        if (empty($this->_config)) {
            $this->_config = Mage::getConfig();
        }
        return $this->_config;
    }

    /**
     * Set config
     *
     * @param Mage_Core_Model_Config $config
     */
    public function setConfig($config)
    {
        $this->_config = $config;
    }

    /**
     * Set store
     *
     * @param Mage_Core_Model_Store $store
     */
    public function setStore($store)
    {
        $this->_store = $store;
    }

    /**
     * Get store
     *
     * @param null|string|bool|int|Mage_Core_Model_Store $storeName
     * @return Mage_Core_Model_Store
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getStore($storeName = null)
    {
        if (empty($this->_store)) {
            $this->_store = Mage::app()->getStore($storeName);
        }
        return $this->_store;
    }

    /**
     * Set website
     * @param Mage_Core_Model_Website $website
     */
    public function setWebsite($website)
    {
        $this->_website = $website;
    }

    /**
     * Get website
     * @param string $websiteCode
     * @return Mage_Core_Model_Website
     */
    public function getWebsite($websiteCode)
    {
        if (empty($this->_website)) {
            $this->_website =  Mage::app()->getWebsite($websiteCode);
        }
        return $this->_website;
    }

    /**
     * Get Captcha
     *
     * @param string $formId
     * @return Mage_Captcha_Model_Interface
     */
    public function getCaptcha($formId)
    {
        if (!array_key_exists($formId, $this->_captcha)) {
            $type = ucfirst($this->getConfigNode('type'));
            $this->_captcha[$formId] = $this->getConfig()->getModelInstance(
                'Mage_Captcha_Model_' . $type,
                array(
                    'params' => array('formId' => $formId, 'helper' => $this)
                )
            );
        }
        return $this->_captcha[$formId];
    }

    /**
     * Returns value of the node with respect to current area (frontend or backend)
     *
     * @param string $id The last part of XML_PATH_$area_CAPTCHA_ constant (case insensitive)
     * @param Mage_Core_Model_Store $store
     * @return Mage_Core_Model_Config_Element
     */
    public function getConfigNode($id, $store = null)
    {
        $areaCode = $this->getStore($store)->isAdmin() ? 'admin' : 'customer';
        return $this->getStore($store)->getConfig( $areaCode . '/captcha/' . $id, $store);
    }

    /**
     * Get list of available fonts
     * Return format:
     * [['arial'] => ['label' => 'Arial', 'path' => '/www/magento/fonts/arial.ttf']]
     *
     * @return array
     */
    public function getFonts()
    {
        $node = $this->getConfig()->getNode(Mage_Captcha_Helper_Data::XML_PATH_CAPTCHA_FONTS);
        $fonts = array();
        if ($node) {
            $libDir = $this->_dirs->getDir(Mage_Core_Model_Dir::LIB);
            foreach ($node->children() as $fontName => $fontNode) {
                $fonts[$fontName] = array(
                    'label' => (string)$fontNode->label,
                    'path' => $libDir . DIRECTORY_SEPARATOR . $fontNode->path
                );
            }
        }
        return $fonts;
    }

    /**
     * Get captcha image directory
     *
     * @param mixed $website
     * @return string
     */
    public function getImgDir($website = null)
    {
        $captchaDir = $this->_dirs->getDir(Mage_Core_Model_Dir::MEDIA) . DIRECTORY_SEPARATOR . 'captcha'
            . DIRECTORY_SEPARATOR . $this->getWebsite($website)->getCode();
        $io = new Varien_Io_File();
        $io->checkAndCreateFolder($captchaDir, 0755);
        return $captchaDir;
    }

    /**
     * Get captcha image base URL
     *
     * @param mixed $website
     * @return string
     */
    public function getImgUrl($website = null)
    {
        return $this->getStore()->getBaseUrl('media') . 'captcha' . '/' . $this->getWebsite($website)->getCode() . '/';
    }
}

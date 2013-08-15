<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml header block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Page_Header extends Mage_Adminhtml_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'page/header.phtml';

    /**
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * @var Mage_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Mage_Backend_Model_Auth_Session $authSession
     * @param Mage_Core_Model_StoreManagerInterface $storeManager
     * @param Mage_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Model_Auth_Session $authSession,
        Mage_Core_Model_StoreManagerInterface $storeManager,
        Mage_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_authSession = $authSession;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }


    public function getHomeLink()
    {
        return $this->helper('Mage_Backend_Helper_Data')->getHomePageUrl();
    }

    public function getUser()
    {
        return $this->_authSession->getUser();
    }

    public function getLogoutLink()
    {
        return $this->getUrl('adminhtml/auth/logout');
    }

    /**
     * Check if noscript notice should be displayed
     *
     * @return boolean
     */
    public function displayNoscriptNotice()
    {
        return Mage::getStoreConfig('web/browser_capabilities/javascript');
    }
}

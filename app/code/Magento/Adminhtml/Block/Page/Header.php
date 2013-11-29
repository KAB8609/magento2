<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml header block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Page;

class Header extends \Magento\Adminhtml\Block\Template
{
    protected $_template = 'page/header.phtml';

    /**
     * Backend data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData = null;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Backend\Helper\Data $backendData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Backend\Helper\Data $backendData,
        array $data = array()
    ) {
        $this->_backendData = $backendData;
        $this->_authSession = $authSession;
        parent::__construct($context, $data);
    }

    public function getHomeLink()
    {
        return $this->_backendData->getHomePageUrl();
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
        return $this->_storeConfig->getConfig('web/browser_capabilities/javascript');
    }
}

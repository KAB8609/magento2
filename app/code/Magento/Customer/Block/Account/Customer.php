<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Account;

class Customer extends \Magento\View\Block\Template
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Core\Helper\Data $coreData,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_customerSession = $session;
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */

    public function customerLoggedIn()
    {
        return (bool)$this->_customerSession->isLoggedIn();
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->_helperFactory->get('Magento_Customer_Helper_Data')->getCustomerName();
    }
}

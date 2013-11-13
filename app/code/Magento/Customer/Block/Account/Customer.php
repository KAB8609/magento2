<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Account;

class Customer extends \Magento\Core\Block\Template
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Customer\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Customer\Model\Session $session,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $data);
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

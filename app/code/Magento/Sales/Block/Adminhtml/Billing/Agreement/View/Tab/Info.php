<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml billing agreement info tab
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Billing\Agreement\View\Tab;

class Info extends \Magento\Backend\Block\Template
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_template = 'billing/agreement/view/tab/info.phtml';

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General Information');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('General Information');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Retrieve billing agreement model
     *
     * @return \Magento\Sales\Model\Billing\Agreement
     */
    protected function _getBillingAgreement()
    {
        return \Mage::registry('current_billing_agreement');
    }

    /**
     * Set data to block
     *
     * @return string
     */
    protected function _toHtml()
    {
        $agreement = $this->_getBillingAgreement();
        $this->setReferenceId($agreement->getReferenceId());
        $customer = \Mage::getModel('\Magento\Customer\Model\Customer')->load($agreement->getCustomerId());
        $this->setCustomerUrl(
            $this->getUrl('*/customer/edit', array('id' => $customer->getId()))
        );
        $this->setCustomerEmail($customer->getEmail());
        $this->setStatus($agreement->getStatusLabel());
        $this->setCreatedAt(
            $this->helper('\Magento\Core\Helper\Data')->formatDate($agreement->getCreatedAt(), 'short', true)
        );
        $this->setUpdatedAt(
             ($agreement->getUpdatedAt())
                ? $this->helper('\Magento\Core\Helper\Data')->formatDate($agreement->getUpdatedAt(), 'short', true) : __('N/A')
        );

        return parent::_toHtml();
    }
}

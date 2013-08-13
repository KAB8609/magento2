<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Customer Account Payment Profiles form block
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Block_Adminhtml_Customer_Edit_Tab_Payment_Profile
    extends Enterprise_Pbridge_Block_Iframe_Abstract
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Block template
     * @var string
     */
    protected $_template = 'customer/edit/tab/payment/profile.phtml';

    /**
     * Default iframe template
     *
     * @var string
     */
    protected $_iframeTemplate = 'Enterprise_Pbridge::iframe.phtml';

    /**
     * Default iframe height
     *
     * @var string
     */
    protected $_iframeHeight = '600';

    /**
     * Getter for label
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Credit Cards');
    }

    /**
     * Getter for title
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Credit Cards');
    }

    /**
     * Whether tab can be shown
     * @return bool
     */
    public function canShowTab()
    {
        if (Mage::registry('current_customer')->getId() && $this->_isProfileEnable()) {
            return true;
        }
        return false;
    }

    /**
     * Whether tab is hidden
     * @return bool
     */
    public function isHidden()
    {
        if (Mage::registry('current_customer')->getId() && $this->_isProfileEnable()) {
            return false;
        }
        return true;
    }

    /**
     * Check if payment profiles enabled
     * @return bool
     */
    protected function _isProfileEnable()
    {
        return Mage::getStoreConfigFlag('payment/pbridge/profilestatus', $this->_getCurrentStore());
    }

    /**
     * Precessor tab ID getter
     *
     * @return string
     */
    public function getAfter()
    {
        return 'reviews';
    }

    /**
     * Return iframe source URL
     * @return string
     */
    public function getSourceUrl()
    {
        $helper = Mage::helper('Enterprise_Pbridge_Helper_Data');
        $helper->setStoreId($this->_getCurrentStore()->getId());
        return $helper->getPaymentProfileUrl(
            array(
                'billing_address' => $this->_getAddressInfo(),
                'css_url'         => null,
                'customer_id'     => $this->getCustomerIdentifier(),
                'customer_name'   => $this->getCustomerName(),
                'customer_email'  => $this->getCustomerEmail()
            )
        );
    }

    /**
     * Get current customer object
     *
     * @return null|Magento_Customer_Model_Customer
     */
    protected function _getCurrentCustomer()
    {
        if (Mage::registry('current_customer') instanceof Magento_Customer_Model_Customer) {
            return Mage::registry('current_customer');
        }

        return null;
    }

    /**
     * Return store for current context
     *
     * @return Magento_Core_Model_Store
     */
    protected function _getCurrentStore()
    {
        return $this->_getCurrentCustomer()->getStore();
    }
}

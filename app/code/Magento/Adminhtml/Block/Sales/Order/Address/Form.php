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
 * Adminhtml sales order edit address block
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Order\Address;

class Form
    extends \Magento\Adminhtml\Block\Sales\Order\Create\Form\Address
{
    protected $_template = 'sales/order/address/form.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Customer\Model\FormFactory $customerFormFactory
     * @param \Magento\Adminhtml\Helper\Addresses $adminhtmlAddresses
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Adminhtml\Model\Session\Quote $sessionQuote
     * @param \Magento\Adminhtml\Model\Sales\Order\Create $orderCreate
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\FormFactory $customerFormFactory,
        \Magento\Adminhtml\Helper\Addresses $adminhtmlAddresses,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Adminhtml\Model\Session\Quote $sessionQuote,
        \Magento\Adminhtml\Model\Sales\Order\Create $orderCreate,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct(
            $addressFactory, $customerFormFactory, $adminhtmlAddresses, $formFactory,
            $sessionQuote, $orderCreate, $coreData, $context, $data
        );
    }

    /**
     * Order address getter
     *
     * @return \Magento\Sales\Model\Order\Address
     */
    protected function _getAddress()
    {
        return $this->_coreRegistry->registry('order_address');
    }

    /**
     * Define form attributes (id, method, action)
     *
     * @return \Magento\Adminhtml\Block\Sales\Order\Create\Billing\Address
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $this->_form->setId('edit_form');
        $this->_form->setMethod('post');
        $this->_form->setAction($this->getUrl('*/*/addressSave', array('address_id'=>$this->_getAddress()->getId())));
        $this->_form->setUseContainer(true);
        return $this;
    }

    /**
     * Form header text getter
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Order Address Information');
    }

    /**
     * Return Form Elements values
     *
     * @return array
     */
    public function getFormValues()
    {
        return $this->_getAddress()->getData();
    }
}

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
 * Adminhtml creditmemo create
 */

namespace Magento\Sales\Block\Adminhtml\Order\Creditmemo;

class Create extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $coreData, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'adminhtml_order_creditmemo';
        $this->_mode = 'create';

        parent::_construct();

        $this->_removeButton('delete');
        $this->_removeButton('save');
    }

    /**
     * Retrieve creditmemo model instance
     *
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_coreRegistry->registry('current_creditmemo');
    }

    public function getHeaderText()
    {
        if ($this->getCreditmemo()->getInvoice()) {
            $header = __('New Credit Memo for Invoice #%1', $this->getCreditmemo()->getInvoice()->getIncrementId());
        } else {
            $header = __('New Credit Memo for Order #%1', $this->getCreditmemo()->getOrder()->getRealOrderId());
        }

        return $header;
    }

    public function getBackUrl()
    {
        return $this->getUrl(
            'sales/order/view',
            array('order_id' => $this->getCreditmemo() ? $this->getCreditmemo()->getOrderId() : null)
        );
    }
}
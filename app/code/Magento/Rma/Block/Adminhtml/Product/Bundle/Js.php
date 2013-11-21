<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block\Adminhtml\Product\Bundle;

class Js extends \Magento\Adminhtml\Block\Template
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

    /**
     * Get url for Bundle AJAX Action
     *
     * @return string
     */
    public function getLoadBundleUrl()
    {
        return $this->getUrl('adminhtml/*/showBundleItems');
    }

    /**
     * Get url for Details AJAX Action
     *
     * @return string
     */
    public function getLoadAttributesUrl()
    {
        return $this->getUrl('adminhtml/*/loadNewAttributes', array(
            'order_id' => $this->_coreRegistry->registry('current_order')->getId()
        ));
    }

    /**
     * Get load order id
     *
     * @return int
     */
    public function getLoadOrderId()
    {
        return $this->getRequest()->getParam('order_id');
    }
}

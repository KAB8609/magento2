<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Adminhtml\Order\Status;

class Edit extends \Magento\Sales\Block\Adminhtml\Order\Status\NewStatus
{
    protected function _construct()
    {
        parent::_construct();
        $this->_mode = 'edit';
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Edit Order Status');
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml dashboard orders diagram
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Backend\Block\Dashboard\Tab;

class Orders extends \Magento\Backend\Block\Dashboard\Graph
{
    /**
     * Initialize object
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setHtmlId('orders');
        parent::_construct();
    }

    /**
     * Prepare chart data
     *
     * @return void
     */
    protected function _prepareData()
    {
        $this->setDataHelperName('Magento\Backend\Helper\Dashboard\Order');
        $this->getDataHelper()->setParam('store', $this->getRequest()->getParam('store'));
        $this->getDataHelper()->setParam('website', $this->getRequest()->getParam('website'));
        $this->getDataHelper()->setParam('group', $this->getRequest()->getParam('group'));

        $this->setDataRows('quantity');
        $this->_axisMaps = array(
            'x' => 'range',
            'y' => 'quantity'
        );

        parent::_prepareData();
    }
}

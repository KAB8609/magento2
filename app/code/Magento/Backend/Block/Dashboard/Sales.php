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
 * Adminhtml dashboard sales statistics bar
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Backend\Block\Dashboard;

use Magento\Adminhtml\Block\Widget;

class Sales extends \Magento\Backend\Block\Dashboard\Bar
{
    protected $_template = 'dashboard/salebar.phtml';

    /**
     * @var \Magento\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @param \Magento\Module\Manager $moduleManager
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Reports\Model\Resource\Order\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Module\Manager $moduleManager,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Reports\Model\Resource\Order\CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->_moduleManager = $moduleManager;
        parent::__construct($context, $collectionFactory, $data);
    }

    protected function _prepareLayout()
    {
        if (!$this->_moduleManager->isEnabled('Magento_Reports')) {
            return $this;
        }
        $isFilter = $this->getRequest()->getParam('store')
            || $this->getRequest()->getParam('website')
            || $this->getRequest()->getParam('group');

        $collection = $this->_collectionFactory->create()
            ->calculateSales($isFilter);

        if ($this->getRequest()->getParam('store')) {
            $collection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        } else if ($this->getRequest()->getParam('website')) {
            $storeIds = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $collection->addFieldToFilter('store_id', array('in' => $storeIds));
        } else if ($this->getRequest()->getParam('group')) {
            $storeIds = $this->_storeManager->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $collection->addFieldToFilter('store_id', array('in' => $storeIds));
        }

        $collection->load();
        $sales = $collection->getFirstItem();

        $this->addTotal(__('Lifetime Sales'), $sales->getLifetime());
        $this->addTotal(__('Average Orders'), $sales->getAverage());
    }
}

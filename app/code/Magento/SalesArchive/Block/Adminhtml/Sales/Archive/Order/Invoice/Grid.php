<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Archive invoices grid block
 *
 */

namespace Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order\Invoice;

class Grid
    extends \Magento\Adminhtml\Block\Sales\Invoice\Grid
{
    /**
     * Core url
     *
     * @var \Magento\Core\Helper\Url
     */
    protected $_coreUrl = null;

    /**
     * @param \Magento\Core\Helper\Url $coreUrl
     * @param \Magento\Sales\Model\Order\InvoiceFactory $invoiceFactory
     * @param \Magento\Sales\Model\Resource\Order\Invoice\Grid\CollectionFactory $collectionFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Url $coreUrl,
        \Magento\Sales\Model\Order\InvoiceFactory $invoiceFactory,
        \Magento\Sales\Model\Resource\Order\Invoice\Grid\CollectionFactory $collectionFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        array $data = array()
    ) {
        $this->_coreUrl = $coreUrl;
        parent::__construct($invoiceFactory, $collectionFactory, $coreData, $context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_invoice_grid_archive');
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'Magento\SalesArchive\Model\Resource\Order\Invoice\Collection';
    }

    /**
     * Retrieve grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
         return $this->getUrl('adminhtml/*/invoicesgrid', array('_current' => true));
    }

    /**
     * Retrieve grid export types
     *
     * @return array|false
     */
    public function getExportTypes()
    {
        if (!empty($this->_exportTypes)) {
            foreach ($this->_exportTypes as $exportType) {
                $url = $this->_coreUrl->removeRequestParam($exportType->getUrl(), 'action');
                $exportType->setUrl($this->_coreUrl->addRequestParam($url, array('action' => 'invoice')));
            }
            return $this->_exportTypes;
        }
        return false;
    }
}

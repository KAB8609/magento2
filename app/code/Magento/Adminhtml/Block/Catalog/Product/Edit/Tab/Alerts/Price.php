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
 * Sign up for an alert when the product price changes grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Alerts;

class Price extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * @var \Magento\ProductAlert\Model\PriceFactory
     */
    protected $_priceFactory;

    /**
     * @param \Magento\ProductAlert\Model\PriceFactory $priceFactory
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param array $data
     */
    public function __construct(
        \Magento\ProductAlert\Model\PriceFactory $priceFactory,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        array $data = array()
    ) {
        $this->_priceFactory = $priceFactory;
        $this->_catalogData = $catalogData;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('alertPrice');
        $this->setDefaultSort('add_date');
        $this->setDefaultSort('DESC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
        $this->setEmptyText(__('There are no customers for this alert.'));
    }

    protected function _prepareCollection()
    {
        $productId = $this->getRequest()->getParam('id');
        $websiteId = 0;
        if ($store = $this->getRequest()->getParam('store')) {
            $websiteId = $this->_storeManager->getStore($store)->getWebsiteId();
        }
        if ($this->_catalogData->isModuleEnabled('Magento_ProductAlert')) {
            $collection = $this->_priceFactory->create()->getCustomerCollection()
                ->join($productId, $websiteId);
            $this->setCollection($collection);
        }
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('firstname', array(
            'header'    => __('First Name'),
            'index'     => 'firstname',
        ));

        $this->addColumn('lastname', array(
            'header'    => __('Last Name'),
            'index'     => 'lastname',
        ));

        $this->addColumn('email', array(
            'header'    => __('Email'),
            'index'     => 'email',
        ));

        $this->addColumn('price', array(
            'header'    => __('Price'),
            'index'     => 'price',
            'type'      => 'currency',
            'currency_code'
                        => $this->_storeConfig->getConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE)
        ));

        $this->addColumn('add_date', array(
            'header'    => __('Subscribe Date'),
            'index'     => 'add_date',
            'type'      => 'date'
        ));

        $this->addColumn('last_send_date', array(
            'header'    => __('Last Notified'),
            'index'     => 'last_send_date',
            'type'      => 'date'
        ));

        $this->addColumn('send_count', array(
            'header'    => __('Send Count'),
            'index'     => 'send_count',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        $productId = $this->getRequest()->getParam('id');
        $storeId   = $this->getRequest()->getParam('store', 0);
        if ($storeId) {
            $storeId = $this->_storeManager->getStore($storeId)->getId();
        }
        return $this->getUrl('adminhtml/catalog_product/alertsPriceGrid', array(
            'id'    => $productId,
            'store' => $storeId
        ));
    }
}

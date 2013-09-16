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
 * Adminhtml customer view wishlist block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Customer\Edit\Tab\View;

class Wishlist extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Core\Model\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Initial settings
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_view_wishlist_grid');
        $this->setSortable(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setEmptyText(__("There are no items in customer's wishlist at the moment"));
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Adminhtml\Block\Customer\Edit\Tab\View\Wishlist
     */
    protected function _prepareCollection()
    {
        $collection = \Mage::getModel('Magento\Wishlist\Model\Item')->getCollection()
            ->addCustomerIdFilter($this->_coreRegistry->registry('current_customer')->getId())
            ->addDaysInWishlist()
            ->addStoreData()
            ->setInStockFilter(true);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Adminhtml\Block\Customer\Edit\Tab\View\Wishlist
     */
    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
            'header'    => __('ID'),
            'index'     => 'product_id',
            'type'      => 'number',
            'width'     => '100px'
        ));

        $this->addColumn('product_name', array(
            'header'    => __('Product'),
            'index'     => 'product_name',
            'renderer'  => 'Magento\Adminhtml\Block\Customer\Edit\Tab\View\Grid\Renderer\Item'
        ));

        if (!\Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store', array(
                'header'    => __('Add Locale'),
                'index'     => 'store_id',
                'type'      => 'store',
                'width'     => '160px',
            ));
        }

        $this->addColumn('added_at', array(
            'header'    => __('Add Date'),
            'index'     => 'added_at',
            'type'      => 'date',
            'width'     => '140px',
        ));

        $this->addColumn('days', array(
            'header'    => __('Days in Wish List'),
            'index'     => 'days_in_wishlist',
            'type'      => 'number',
            'width'     => '140px',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Get headers visibility
     *
     * @return bool
     */
    public function getHeadersVisibility()
    {
        return ($this->getCollection()->getSize() >= 0);
    }

    /**
     * Get row url
     *
     * @param \Magento\Wishlist\Model\Item $item
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product/edit', array('id' => $row->getProductId()));
    }
}

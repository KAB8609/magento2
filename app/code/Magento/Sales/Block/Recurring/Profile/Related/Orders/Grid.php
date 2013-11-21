<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Recurring\Profile\Related\Orders;

/**
 * Recurring profile related orders grid
 */
class Grid extends \Magento\Sales\Block\Recurring\Profile\View
{
    /**
     * @var \Magento\Sales\Model\Resource\Order\Collection
     */
    protected $_orderCollection;
    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_config;

    /**
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Sales\Model\Resource\Order\Collection $collection
     * @param \Magento\Sales\Model\Order\Config $config
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Sales\Model\Resource\Order\Collection $collection,
        \Magento\Sales\Model\Order\Config $config,
        \Magento\Core\Helper\Data $coreData,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $storeManager, $locale, $coreData, $data);
        $this->_orderCollection = $collection;
        $this->_config = $config;
    }
    /**
     * Prepare related orders collection
     *
     * @param array|string $fieldsToSelect
     */
    protected function _prepareRelatedOrders($fieldsToSelect = '*')
    {
        if (null === $this->_relatedOrders) {
            $this->_relatedOrders = $this->_orderCollection
                ->addFieldToSelect($fieldsToSelect)
                ->addFieldToFilter('customer_id', $this->_registry->registry('current_customer')->getId())
                ->addRecurringProfilesFilter($this->_profile->getId())
                ->setOrder('entity_id', 'desc');
        }
    }

    /**
     * Prepare related grid data
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->_prepareRelatedOrders(array(
            'increment_id', 'created_at', 'customer_firstname', 'customer_lastname', 'base_grand_total', 'status'
        ));
        $this->_relatedOrders->addFieldToFilter('state', array(
            'in' => $this->_config->getVisibleOnFrontStates()
        ));

        $pager = $this->getLayout()->createBlock('Magento\Page\Block\Html\Pager')
            ->setCollection($this->_relatedOrders)->setIsOutputRequired(false);
        $this->setChild('pager', $pager);

        $this->setGridColumns(array(
            new \Magento\Object(array(
                'index' => 'increment_id',
                'title' => __('Order #'),
                'is_nobr' => true,
                'width' => 1,
            )),
            new \Magento\Object(array(
                'index' => 'created_at',
                'title' => __('Date'),
                'is_nobr' => true,
                'width' => 1,
            )),
            new \Magento\Object(array(
                'index' => 'customer_name',
                'title' => __('Customer Name'),
            )),
            new \Magento\Object(array(
                'index' => 'base_grand_total',
                'title' => __('Order Total'),
                'is_nobr' => true,
                'width' => 1,
                'is_amount' => true,
            )),
            new \Magento\Object(array(
                'index' => 'status',
                'title' => __('Order Status'),
                'is_nobr' => true,
                'width' => 1,
            )),
        ));

        $orders = array();
        foreach ($this->_relatedOrders as $order) {
            $orders[] = new \Magento\Object(array(
                'increment_id' => $order->getIncrementId(),
                'created_at' => $this->formatDate($order->getCreatedAt()),
                'customer_name' => $order->getCustomerName(),
                'base_grand_total' => $this->helper('Magento\Core\Helper\Data')->formatCurrency(
                    $order->getBaseGrandTotal(), false
                ),
                'status' => $order->getStatusLabel(),
                'increment_id_link_url' => $this->getUrl('sales/order/view/', array('order_id' => $order->getId())),
            ));
        }
        if ($orders) {
            $this->setGridElements($orders);
        }
    }
}

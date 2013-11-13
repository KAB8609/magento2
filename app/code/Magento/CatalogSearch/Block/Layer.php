<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layered Navigation block for search
 *
 */
namespace Magento\CatalogSearch\Block;

class Layer extends \Magento\Catalog\Block\Layer\View
{
    /**
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $_engineProvider;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Catalog search data
     *
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $_catalogSearchData = null;

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Catalog search layer
     *
     * @var \Magento\CatalogSearch\Model\Layer
     */
    protected $_catalogSearchLayer;

    /**
     * Construct
     *
     * @param \Magento\CatalogSearch\Model\Layer $catalogLayer
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     * @param \Magento\CatalogSearch\Model\Layer $catalogSearchLayer
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\CatalogSearch\Model\Layer $catalogLayer,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider,
        \Magento\CatalogSearch\Helper\Data $catalogSearchData,
        \Magento\CatalogSearch\Model\Layer $catalogSearchLayer,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_engineProvider = $engineProvider;
        $this->_coreRegistry = $registry;
        $this->_catalogSearchData = $catalogSearchData;
        $this->_catalogSearchLayer = $catalogSearchLayer;
        $this->_storeManager = $storeManager;
        parent::__construct($catalogLayer, $coreData, $context, $data);
    }

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_coreRegistry->register('current_layer', $this->getLayer(), true);
    }

    /**
     * Initialize blocks names
     */
    protected function _initBlocks()
    {
        parent::_initBlocks();

        $this->_attributeFilterBlockName = 'Magento\CatalogSearch\Block\Layer\Filter\Attribute';
    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock()
    {
        $_isLNAllowedByEngine = $this->_engineProvider->get()->isLayeredNavigationAllowed();
        if (!$_isLNAllowedByEngine) {
            return false;
        }
        $availableResCount = (int)$this->_storeManager->getStore()
            ->getConfig(\Magento\CatalogSearch\Model\Layer::XML_PATH_DISPLAY_LAYER_COUNT);

        if (!$availableResCount
            || ($availableResCount > $this->getLayer()->getProductCollection()->getSize())
        ) {
            return parent::canShowBlock();
        }
        return false;
    }
}

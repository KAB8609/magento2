<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Product\ProductList;

class Promotion extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Layer factory
     *
     * @var \Magento\Catalog\Model\LayerFactory
     */
    protected $_layerFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\Layer $catalogLayer
     * @param \Magento\Catalog\Model\LayerFactory $layerFactory
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Core\Model\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Math\Random $mathRandom,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Layer $catalogLayer,
        \Magento\Catalog\Model\LayerFactory $layerFactory,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory,
        array $data = array()
    ) {
        $this->_layerFactory = $layerFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct(
            $context,
            $coreData,
            $catalogConfig,
            $registry,
            $taxData,
            $catalogData,
            $mathRandom,
            $categoryFactory,
            $catalogLayer,
            $data
        );
    }

    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            /** @var \Magento\Catalog\Model\Resource\Product\Collection $collection */
            $collection = $this->_productCollectionFactory->create();
            $this->_layerFactory->create()->prepareProductCollection($collection);

            $collection->addAttributeToFilter('promotion', 1)
                ->addStoreFilter();

            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
}

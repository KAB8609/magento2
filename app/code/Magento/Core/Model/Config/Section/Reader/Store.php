<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\Config\Section\Reader;

class Store
{
    /**
     * @var \Magento\Core\Model\Config\Initial
     */
    protected $_initialConfig;

    /**
     * @var \Magento\Core\Model\Config\SectionPool
     */
    protected $_sectionPool;

    /**
     * @var \Magento\Core\Model\Config\Section\Store\Converter
     */
    protected $_converter;

    /**
     * @var \Magento\Core\Model\Resource\Config\Value\Collection\ScopedFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Core\Model\StoreFactory
     */
    protected $_storeFactory;

    /**
     * @var \Magento\Core\Model\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\Core\Model\Config\Initial $initialConfig
     * @param \Magento\Core\Model\Config\SectionPool $sectionPool
     * @param \Magento\Core\Model\Config\Section\Store\Converter $converter
     * @param \Magento\Core\Model\Resource\Config\Value\Collection\ScopedFactory $collectionFactory
     * @param \Magento\Core\Model\StoreFactory $storeFactory
     * @param \Magento\Core\Model\App\State $appState
     */
    public function __construct(
        \Magento\Core\Model\Config\Initial $initialConfig,
        \Magento\Core\Model\Config\SectionPool $sectionPool,
        \Magento\Core\Model\Config\Section\Store\Converter $converter,
        \Magento\Core\Model\Resource\Config\Value\Collection\ScopedFactory $collectionFactory,
        \Magento\Core\Model\StoreFactory $storeFactory,
        \Magento\Core\Model\App\State $appState
    ) {
        $this->_initialConfig = $initialConfig;
        $this->_sectionPool = $sectionPool;
        $this->_converter = $converter;
        $this->_collectionFactory = $collectionFactory;
        $this->_storeFactory = $storeFactory;
        $this->_appState = $appState;
    }

    /**
     * Read configuration by code
     *
     * @param string $code
     * @return array
     */
    public function read($code)
    {
        if ($this->_appState->isInstalled()) {
            $store = $this->_storeFactory->create();
            $store->load($code);
            $websiteConfig = $this->_sectionPool->getSection('website', $store->getWebsite()->getCode())->getValue();
            $config = array_replace_recursive($websiteConfig, $this->_initialConfig->getStore($code));

            $collection = $this->_collectionFactory->create(array('scope' => 'stores', 'scopeId' => $store->getId()));
            $dbStoreConfig = array();
            foreach ($collection as $item) {
                $dbStoreConfig[$item->getPath()] = $item->getValue();
            }
            $config = $this->_converter->convert($dbStoreConfig, $config);
        } else {
            $websiteConfig = $this->_sectionPool->getSection('website', 'default')->getValue();
            $config = $this->_converter->convert($websiteConfig, $this->_initialConfig->getStore($code));
        }
        return $config;
    }
} 

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Event resource setup
 */
namespace Magento\CatalogEvent\Model\Resource;

class Setup extends \Magento\Sales\Model\Resource\Setup
{
    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @param \Magento\Core\Model\Resource\Setup\Context $context
     * @param string $resourceName
     * @param \Magento\App\CacheInterface $cache
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGrCollFactory
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Cms\Model\BlockFactory $modelBlockFactory
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Core\Model\Resource\Setup\Context $context,
        $resourceName,
        \Magento\App\CacheInterface $cache,
        \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $attrGrCollFactory,
        \Magento\Core\Model\Config $config,
        \Magento\Cms\Model\BlockFactory $modelBlockFactory,
        $moduleName = 'Magento_CatalogEvent',
        $connectionName = ''
    ) {
        $this->_blockFactory = $modelBlockFactory;
        parent::__construct($context, $resourceName, $cache, $attrGrCollFactory, $config, $moduleName, $connectionName);
    }

    /**
     * Get model block factory
     *
     * @return \Magento\Cms\Model\BlockFactory
     */
    public function getBlockFactory()
    {
        return $this->_blockFactory;
    }
}
